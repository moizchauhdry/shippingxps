<?php

namespace App\Http\Controllers;

use App\Events\CustomFormFilled;
use App\Events\PackageConsolidated;
use App\Events\PackageShipped;
use App\Events\PackageShippingServiceSelected;
use App\Events\ServiceRequestedEvent;
use App\Events\ServiceRequestUpdatedEvent;
use App\Models\Address;
use App\Models\Payment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use GuzzleHttp\Client;
use App\Models\Order;
use App\Models\Package;
use App\Models\Country;
use App\Models\OrderItem;
use App\Models\PackageBox;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Models\SiteSetting;
use App\Models\Shipping;
use App\Models\Warehouse;
use App\Notifications\CustomerPackageRequestNotification;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;

use function Couchbase\basicDecoderV1;


class PackageController extends Controller
{
    public $token = 'm99PyQIqoq5GmAKjs1wOTAbhQ0ozkc0s';
    public $customer_id = '12339140';
    public $integration_id = '59690';
    public $status_list = ['arrived', 'labeled', 'shipped', 'delivered', 'rejected'];

    private function calculate_storage_fee($id)
    {
        $package = Package::find($id);
        $orders = $package->orders;

        $fee = SiteSetting::where('name', 'storage_fee')->first()->value;

        $storageFee = 0;
        foreach ($orders as $item) {
            if ($item->arrived_at != null) {
                $lastDate = Carbon::make($item->arrived_at)->addDays(75);
                $currentDate = Carbon::now();
                if (strtotime($currentDate) > strtotime($lastDate)) {
                    $daysDifference = $currentDate->diffInDays($lastDate);
                    $storageFee += $daysDifference * $fee;
                }
            }
        }

        $package->storage_fee = $storageFee;
        $package->save();
        return $storageFee;
    }

    public function index(Request $request)
    {
        $status = $request->has('status') ? $request->status : 'packages';

        $query = Package::with('customer', 'warehouse', 'child_packages');

        if ($status == 'rejected') {
            $query->where('status', 'rejected');
        } else {
            $query->where('status', '<>', 'rejected');
        }

        if (Auth::user()->type == 'customer') {
            $query->where('customer_id', Auth::user()->id);
        }

        if (!empty($request->pkg_id)) {
            $query->where('id', $request->pkg_id);
        }

        if (!empty($request->suit_no)) {
            $suit_no = intval($request->suit_no) - 4000;
            $query->where('customer_id', $suit_no);
        }

        $packages = $query->orderBy('id', 'desc')->paginate(25);

        $open_pkgs_count = Package::where('status', 'open')->where('pkg_type', 'single')->count();

        return Inertia::render('Packages/Index', [
            'pkgs' => $packages,
            'open_pkgs_count' => $open_pkgs_count,
            'filter' => [
                'status' => $status
            ]
        ]);
    }

    public function show($id)
    {
        $packag = Package::with('orders', 'address', 'warehouse', 'customer', 'images', 'serviceRequests', 'child_packages', 'order', 'boxes')
            ->findOrFail($id);

        $child_package_orders = [];
        foreach ($packag->child_packages as $key => $child_package) {
            if ($child_package->order) {
                $child_package_orders[] = [
                    'pkg_id' => $child_package->id,
                    'order_id' => $child_package->order->id,
                    'weight' => $child_package->order->package_weight,
                    'weight_unit' => $child_package->order->weight_unit,
                    'dim_unit' => $child_package->order->dim_unit,
                    'height' => $child_package->order->package_height,
                    'width' => $child_package->order->package_width,
                    'length' => $child_package->order->package_length,
                    'warehouse' => $child_package->order->warehouse->name,
                    'tracking_in' => $child_package->order->tracking_number_in,
                    'images' => $child_package->order->images,
                    'status' => $child_package->status,
                ];
            }
        }

        $services = Service::where('status', '=', '1')->get();
        $serviceRequest = ServiceRequest::where('package_id', $packag->id)->where('service_id', 1)->where('status', 'pending')->first();

        $service_requests = [];
        foreach ($packag->serviceRequests as $req) {
            if ($req->service) {
                $service_requests[] = [
                    'id' => $req->id,
                    'service_title' => $req->service->title,
                    'service_description' => $req->service->description,
                    'status' => $req->status,
                    'customer_message' => $req->customer_message,
                    'admin_message' => $req->admin_message,
                    'price' => $req->price,
                    'date' => $req->created_at,
                ];
            }
        }

        $service_requests_service_ids = [];
        foreach ($packag->serviceRequests as $request) {
            $service_requests_service_ids[] = $request->service_id;
        }

        $images = [];
        foreach ($packag->images as $image) {
            $images[] = [
                'order_id' => $image->order_id,
                'img_url' => $image->image,
            ];
        }

        $pickup_service_charges = SiteSetting::getByName('pickup_service_charges');
        $order_charges = [];
        foreach ($packag->orders as $order) {
            if ($order->order_origin == 'pickup') {
                $total = 0;
                foreach ($order->items as $item) {
                    $total = $item->unit_price * $item->quantity;
                }
                $sales_tax = ($order->sales_tax / 100) * $total;
                $sub_total = $total + $sales_tax;
                $service_charges = ($pickup_service_charges / 100) * $sub_total;
                $order_charges[] = [
                    'total' => $total,
                    'sales_tax' => $sales_tax,
                    'sub_total' => $sub_total,
                    'service_charges' => $service_charges
                ];
            }
        }

        $subtotal = 0;
        $package_service_requests = [];
        foreach ($packag->serviceRequests as $key => $service_request) {
            if ($service_request->status == 'served') {
                $package_service_requests[] = [
                    'id' => $service_request->id,
                    'name' => $service_request->service->title,
                    'price' => $service_request->price,
                    'amount' => $service_request->price,
                    'child_package_id' => $service_request->child_package_id,
                ];
                $subtotal = $subtotal + $service_request->price;
            }
        }

        $total = $subtotal + $packag->shipping_charges + (float) SiteSetting::getByName('mailout_fee') +
            $this->calculate_storage_fee($packag->id) + $packag->consolidation_fee;

        $eei_charges = 0;
        if ($total >= 2500 || (isset($packag->address->country) && in_array($packag->address->country->iso, ['CN', 'HK', 'RU', 'VE']))) {
            $eei_charges = (float) SiteSetting::getByName('eei_charges');
            $total = $total + $eei_charges;
        }

        $shipping_address = Address::where('user_id', Auth::user()->id)->get();

        $packag->update(['grand_total' => $total]);

        $package_boxes = [];
        foreach ($packag->boxes as $pkg_box) {
            $package_boxes[] = [
                'id' => $pkg_box->id,
                'weight_unit' => $pkg_box->weight_unit,
                'dim_unit' => $pkg_box->dim_unit,
                'weight' => $pkg_box->weight,
                'length' => $pkg_box->length,
                'width' => $pkg_box->width,
                'height' => $pkg_box->height,
            ];
        }

        $countries = Country::get(['id', 'nicename as name', 'iso'])->toArray();
        $service_request_pending_count = ServiceRequest::where('package_id', $packag->id)->where('status', 'pending')->count();

        if ($packag->status == 'shipping_service_selected') {
            $packag->update(['status' => 'checkout']);
            event(new PackageShippingServiceSelected($packag));
        }

        return Inertia::render('Packages/Show', [
            'packag' => $packag,
            'child_package_orders' => $child_package_orders,
            'services' => $services,
            'service_requests' => $service_requests,
            'images' => $images,
            'order_charges' => $order_charges,
            'mailout_fee' => (float) SiteSetting::getByName('mailout_fee'),
            'eei_charges' => $eei_charges,
            'shipping_services' => Shipping::getShippingServices(),
            'package_service_requests' => $package_service_requests,
            'shipping_address' => $shipping_address,
            'subtotal' => $subtotal,
            'total' => $total,
            'package_boxes' => $package_boxes,
            'countries' => $countries,
            'service_requests_service_ids' => $service_requests_service_ids,
            'service_request_pending_count' => $service_request_pending_count,
        ]);
    }

    public function custom($package_id, $mode = NULL)
    {
        $packag = Package::with('orders', 'warehouse', 'customer', 'packageItems', 'address')->find($package_id);

        if ($packag->service_code) {
            abort(403);
        }

        $user_id = Auth::user()->id;
        $addresses = Address::where('user_id', $user_id)->get();
        $warehouse = $packag->warehouse;



        $package_items = [];
        foreach ($packag->packageItems as $pkg_item) {
            $package_items[] = [
                'id' => $pkg_item->id,
                'hs_code' => $pkg_item->hs_code,
                'description' => $pkg_item->description,
                'quantity' => $pkg_item->quantity,
                'price' => $pkg_item->unit_price,
                'origin_country' => $pkg_item->origin_country,
                'batteries' => $pkg_item->batteries,
            ];
        }

        $tracking_numbers = [];
        foreach ($packag->orders as $order) {
            $tracking_numbers[] = $order->tracking_number_in;
        }


        $address_book = [];
        $selected_address = '';
        $address_book_id = 0;

        foreach ($addresses as $address) {

            $full_address = $address->fullname . " " . $address->address . "<br>" .
                $address->city . " " . $address->state . " " . $address->zip_code . " " . $address->country->nicename . "<br>" .
                $address->phone;

            if ($selected_address == '') {
                $selected_address = $full_address;
                $address_book_id = $address->id;
            }

            $address_book[$address->id] = [
                'id' => $address->id,
                'label' => $address->fullname . ", " . $address->city . ", " . $address->state . ", " . $address->zip_code,
                'full_address' => $full_address
            ];
        }

        $countries = Country::all(['id', 'nicename as name'])->toArray();

        return Inertia::render('Packages/Customs/Create', [
            'countries' => $countries,
            'package_items' => $package_items,
            'address_book' => $address_book,
            'address_book_id' => $address_book_id,
            'selected_address' => $selected_address,
            'packag' => $packag,
            'warehouse' => $warehouse,
            'tracking_numbers' => $tracking_numbers,
            'package_date' => date('Y-m-d'),
            'mode' => $mode,
        ]);
    }

    public function store(Request $request)
    {
        $package = Package::with('order', 'packageItems')->where('id', $request->package_id)->first();

        $validated = $request->validate([
            'package_items.*.description' => 'required',
            'package_items.*.quantity' => 'required',
            'package_items.*.price' => 'required|gt:0|numeric',
            'package_items.*.origin_country' => 'required',
            'package_items.*.batteries' => 'nullable',
            'package_items.*.hs_code' => 'nullable',
            'shipping_total' => 'required',
            'package_type' => 'required',
            'special_instructions' => 'nullable',
        ], [
            'package_items.*.description.required' => 'The package items description field is required.',
            'package_items.*.quantity.required' => 'The package items quantity field is required.',
            'package_items.*.price.required' => 'The package items price field is required.',
            'package_items.*.price.gt' => 'The package items price must be greater than 0.',
            'package_items.*.origin_country.required' => 'The package items origin country field is required.',
        ]);

        $package->update([
            'status' => 'filled',
            'shipping_total' => $validated['shipping_total'],
            'package_type' => $validated['package_type'],
            'special_instructions' => $request->special_instructions,
        ]);

        OrderItem::where('package_id', $package->id)->delete();
        foreach ($request->package_items as $key => $pkg_item) {
            $order_item = new OrderItem();
            $order_item->package_id = $package->id;
            $order_item->hs_code = $pkg_item['hs_code'] ?? null;
            $order_item->description = $pkg_item['description'];
            $order_item->quantity = $pkg_item['quantity'];
            $order_item->unit_price = $pkg_item['price'];
            $order_item->origin_country = $pkg_item['origin_country'];
            $order_item->batteries = $pkg_item['batteries'] ?? null;
            $order_item->save();
        }

        event(new CustomFormFilled($package));

        return redirect()->route('packages.show', $package->id)->with('success', 'The custom decration form filled successfully.');
    }

    public function edit($id)
    {

        $items = [];

        $user_id = Auth::user()->id;

        $order = Order::with(['items', 'images'])->find($id);
        $items = [];

        foreach ($order->items as $item) {
            $items[] = [
                'id' => $item->id,
                'description' => $item->description,
                'quantity' => $item->quantity,
                'value' => $item->unit_price,
                'origin_country' => $item->origin_country,
                'batteries' => $item->batteries,
            ];
        }

        $addresses = Address::where('user_id', $user_id)->get();

        $address_book = [];

        $first_address = '';
        $address_book_id = 0;

        foreach ($addresses as $address) {

            $full_address = $address->fullname . " " . $address->address . "<br>" . $address->city . " " . $address->state . " " . $address->country . "<br>" . $address->phone;
            if ($first_address == '') {
                $first_address = $full_address;
                $address_book_id = $address->id;
            }

            $address_book[$address->id] = [
                'id' => $address->id,
                'label' => $address->fullname . " " . $address->city . " " . $address->state,
                'full_address' => $full_address
            ];
        }

        $countries = Country::all(['id', 'nicename as name'])->toArray();

        return Inertia::render('Packages/EditPackage', [
            'countries' => $countries,
            'items' => $items,
            'address_book' => $address_book,
            'address_book_id' => $address_book_id,
            'first_address' => $first_address,
            'order' => $order
        ]);
    }

    public function update(Request $request)
    {

        return false;

        $id = $request->input('id');

        $package = Order::find($id);

        $validated = $request->validate([
            'address_book_id' => 'required'
        ]);

        $package->address_book_id = $validated['address_book_id'];


        $package->update();
        $items = $request->input('items');

        $files = $request->file();

        $batteries = [
            0 => 'No Batteries',
            1 => 'Simple Batteries (Shipped on on Fedex)',
            2 => 'Batteries Packaed with Equipment',
            3 => 'Batteries Contained in Equipment'
        ];


        foreach ($items as $key => $item) {

            $item_id = isset($item['id']) ? (int) $item['id'] : 0;

            $order_item = OrderItem::find($item_id);
            //update if existing, else creat new. 

            if (!is_object($order_item)) {
                $order_item = new OrderItem();
            }

            $order_item->name = $item['description'];
            $order_item->description = $item['description'];
            $order_item->quantity = $item['quantity'];
            $order_item->unit_price = $item['value'];
            $order_item->origin_country = $item['origin_country'];
            $order_item->batteries = $item['batteries'];

            $order_item->save();
        }

        return redirect('packages')->with('success', 'Package  Updated !');
    }

    public function commercialInvoice($package_id)
    {
        $package = Package::with(['packageItems', 'warehouse.country'])->find($package_id);
        $warehouse = $package->warehouse;
        $user = User::find($package->customer_id);
        $address = Address::find($package->address_book_id);

        $package_weight = 0;
        if (isset($package->boxes)) {
            $package_weight = $package->boxes->sum('weight');
        }


        $html = view('pdfs.commercial-invoice', [
            'package' => $package,
            'package_weight' => $package_weight,
            'warehouse' => $warehouse,
            'user' => $user,
            'address' => $address
        ])->render();

        $mpdf = new \Mpdf\Mpdf();
        $mpdf->WriteHTML($html);
        //page 2
        $mpdf->AddPage();
        $mpdf->WriteHTML($html);
        //page 3
        $mpdf->AddPage();
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }

    public function serviceRequest(Request $request)
    {

        $service = $request->input('service');
        $service_request = new ServiceRequest();
        $service_request->service_id = $service['id'];
        $service_request->package_id = $request->input('package_id');
        $service_request->price = $service['price'];
        $service_request->status = 'pending';
        $service_request->customer_message = $request->input('customer_message');
        $service_request->save();

        if ($service['keyword'] == 'consolidation') {
            $package = Package::find($request->input('package_id'));
            $package->consolidation_request = 1;
            $package->update();
        }

        event(new ServiceRequestedEvent($service_request));

        //return redirect()->route('packages.show',['id'=>$request->input('package_id')])->with('success', 'Your service request sent to Admin.');
        return redirect()->back()->with('success', 'Your service request sent to Admin.');
    }

    public function serviceHandle(Request $request)
    {

        $service_request_data = $request->input('request');

        $service_reqeust = ServiceRequest::find($service_request_data['id']);
        $service_reqeust->status = $request->input('status');
        $service_reqeust->admin_message = $request->input('admin_message');
        $service_reqeust->price = $service_request_data['price'];
        $service_reqeust->save();

        event(new ServiceRequestUpdatedEvent($service_reqeust));

        return redirect()->back()->with('success', 'Service request updated, customer notfied.');
    }

    public function consolidatePackage(Request $request)
    {
        $package = Package::findOrFail($request->package_id);

        PackageBox::where('package_id', $package->id)->delete();
        foreach ($request->package_boxes as $pkg_box) {
            PackageBox::create([
                'package_id' => $package->id,
                'pkg_type' => 'consolidation',
                'weight_unit' => $pkg_box['weight_unit'],
                'dim_unit' => $pkg_box['dim_unit'],
                'weight' => $pkg_box['weight'],
                'length' => $pkg_box['length'],
                'width' => $pkg_box['width'],
                'height' => $pkg_box['height'],
            ]);
        }

        $package->status = 'consolidated';
        $package->pkg_dim_status = 'done';
        $package->admin_status = 'accepted';
        $package->consolidation_fee = (1.5 * count($package->child_packages)) + 5;
        $package->update();

        event(new PackageConsolidated($package));

        return redirect()->back();
    }

    public function shipPackage(Request $request)
    {
        $data = $request->validate([
            'tracking_out' => 'required',
            'box_id' => 'required'
        ]);

        $pkg_box = PackageBox::find($data['box_id']);
        $pkg_box->update(['tracking_out' => $data['tracking_out']]);

        // event(new PackageShipped($package));

        return redirect()->back()->with('success', 'Package set for shipment.');
    }

    public function setShippingService(Request $request)
    {
        $package = Package::find($request->package_id);
        $package->status = 'shipping_service_selected';

        $package->carrier_code = $request->code;
        $package->service_code = $request->type;
        $package->service_label = $request->name;
        $package->package_type_code = $request->pkg_type;
        $package->currency = "USD";
        $package->markup_fee = $request->markup;
        $package->shipping_charges = $request->total;
        $package->update();

        return redirect()->route('packages.show', $package->id)->with('success', 'The package has been set for shipment.');
    }

    public function removeItem(Request $request)
    {

        // $item_id = $request->input('item_id');
        // OrderItem::find($item_id)->delete();
    }

    public function getStorageFee(Request $request)
    {
        $id = $request->package_id;
        $package = Package::find($id);
        $orders = $package->orders;

        $fee = SiteSetting::where('name', 'storage_fee')->first()->value;

        $storageFee = 0;
        foreach ($orders as $item) {
            if ($item->arrived_at != null) {
                $lastDate = Carbon::make($item->arrived_at)->addDays(75);
                $currentDate = Carbon::now();
                if (strtotime($currentDate) > strtotime($lastDate)) {
                    $daysDifference = $currentDate->diffInDays($lastDate);
                    $storageFee += $daysDifference * $fee;
                }
            }
        }

        $package->storage_fee = $storageFee;
        $package->save();
        return $storageFee;
    }

    public function destroy(Request $request)
    {
        $package = Package::find($request->package_id);
        $package->delete();
        return redirect()->route('packages.index')->with('error', 'The package have been deleted successfully.');
    }

    public function pushPackage($packageID)
    {
        $response = [];
        $package = Package::with(['warehouse', 'orders', 'address'])->find($packageID);
        $multiPieceRequestStatus = ServiceRequest::where('package_id', $package->id)->where('status', 'served')->where('service_id', 5)->first();
        $warehouse = $package->warehouse;
        $sender = [
            'name' => $warehouse->contact_person,
            'company' => $warehouse->name,
            'address1' => $warehouse->address,
            'address2' => ' ',
            'city' => $warehouse->city,
            'state' => $warehouse->state,
            'zip' => $warehouse->zip,
            'country' => $warehouse->country->iso,
            'phone' => $warehouse->phone,
            'email' => $warehouse->email,
        ];

        $address = $package->address;
        $reciever = [
            'name' => $address->fullname,
            'company' => '',
            'address1' => $address->address,
            'address2' => ' ',
            'city' => $address->city,
            'state' => $address->state,
            'zip' => $address->zip_code ?? ' ',
            'country' => $address->country->iso,
            'phone' => $address->phone,
            'email' => null,
        ];

        if ($multiPieceRequestStatus != null) {
            $orders =  $package->orders;
            foreach ($orders as $order) {
                $orderItems = $order->items;
                $items = [];
                foreach ($orderItems as $item) {
                    $items[] = [
                        'productId' => (string)$item->id,
                        'sku' => null,
                        'title' => $item->name,
                        'price' => (string)$item->unit_price ?? null,
                        'quantity' => $item->quantity,
                        'countryOfOrigin' => Country::find($item->origin_country)->iso ?? null,
                        'weight' => null,
                        'imgUrl' => null,
                        'htsNumber' => null,
                        'lineId' => null,
                    ];
                }
                $packageInfo = [
                    [
                        'weight' => (string)$order->package_weight,
                        'length' => (string)$order->package_length,
                        'width' => (string)$order->package_width,
                        'height' => (string)$order->package_height,
                        'insuranceAmount' => NULL,
                        'declaredValue' => $order->declared_value == 0 ? NULL : $order->declared_value,
                    ]
                ];
                $post_params = array(
                    'orderId' => $package->package_no . '-' . sprintf("%05d", $order->id),
                    'orderDate' => date('Y-m-d', strtotime($package->created_at)),
                    'orderNumber' => $package->package_no,
                    'fulfillmentStatus' => 'pending',
                    'shippingService' => $package->package_type_code . ' ' . $package->service_label,
                    'shippingTotal' => (string)$package->shipping_charges,
                    'weightUnit' => $order->weight_unit,
                    'dimUnit' => $order->dim_unit,
                    'dueByDate' => Carbon::now()->addDays(10)->format('Y-m-d'),
                    'orderGroup' => $package->package_no,
                    'contentDescription' => null,
                    'sender' => $sender,
                    'receiver' => $reciever,
                    'items' => $items,
                    'packages' => $packageInfo
                );

                $response[] = json_decode($this->hitApi($post_params), true);
            }
        } else {
            $orders =  $package->orders;
            $ordersIDs = $package->orders()->pluck('id')->toArray();
            $orderItems = OrderItem::whereIn('order_id', $ordersIDs)->get();
            $items = [];
            foreach ($orderItems as $item) {
                $items[] = [
                    'productId' => (string)$item->id,
                    'sku' => null,
                    'title' => $item->name,
                    'price' => (string)$item->unit_price ?? null,
                    'quantity' => $item->quantity,
                    'countryOfOrigin' => Country::find($item->origin_country)->iso ?? null,
                    'weight' => null,
                    'imgUrl' => null,
                    'htsNumber' => null,
                    'lineId' => null,
                ];
            }

            $packageInfo = [
                [
                    'weight' => (string)$package->package_weight,
                    'length' => (string)$package->package_length,
                    'width' => (string)$package->package_width,
                    'height' => (string)$package->package_height,
                    'insuranceAmount' => NULL,
                    'declaredValue' => $package->declared_value == 0 ? NULL : $package->declared_value,
                ]
            ];

            $post_params = array(
                'orderId' => $package->package_no,
                'orderDate' => date('Y-m-d', strtotime($package->created_at)),
                'orderNumber' => $package->package_no,
                'fulfillmentStatus' => 'pending',
                'shippingService' => $package->package_type_code . ' ' . $package->service_label,
                'shippingTotal' => (string)$package->shipping_charges,
                'weightUnit' => $package->weight_unit,
                'dimUnit' => $package->dim_unit,
                'dueByDate' => Carbon::now()->addDays(10)->format('Y-m-d'),
                'orderGroup' => null,
                'contentDescription' => null,
                'sender' => $sender,
                'receiver' => $reciever,
                'items' => $items,
                'packages' => $packageInfo
            );

            $response[] =  json_decode($this->hitApi($post_params), true);
        }

        $count = 0;

        foreach ($response as $res) {
            if (isset($res['ok']) && $res['ok'] == true) {
                $count++;
            }
        }

        return response()->json([
            'okCount' => $count,
            'total' => count($response),
        ]);
    }

    public function hitApi($postParameters)
    {
        try {

            $headers = [
                'cache-control' => 'no-cache',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token
            ];

            $client = new Client();

            $request = $client->put('https://xpsshipper.com/restapi/v1/customers/' . $this->customer_id . '/integrations/' . $this->integration_id . '/orders/' . $postParameters['orderId'], [
                'headers' => $headers,
                'body' => json_encode($postParameters),
                'http_errors' => true,
            ]);

            $response = $request ? $request->getBody()->getContents() : null;

            \Log::info($response);

            return $response;
        } catch (\Exception $ex) {

            $ex_message = $ex->getMessage();

            return $ex_message;
        }
    }

    public function consolidation(Request $request)
    {
        $query = Package::with('customer', 'warehouse')
            ->where('warehouse_id', $request->warehouse_id)
            ->where('status', 'open')
            ->where('pkg_type', 'single');

        if (Auth::user()->type == 'customer') {
            $query->where('customer_id', Auth::user()->id);
        }

        $packages = $query->orderBy('id', 'desc')->get();

        $warehouses = Warehouse::get();

        return Inertia::render('Packages/Consolidation', [
            'pkgs' => $packages,
            'warehouses' => $warehouses,
        ]);
    }

    public function storeConsolidation(Request $request)
    {
        if ($request->package_consolidation == []) {
            return redirect()->back()->with('error', 'Please select package for consolidation.');
        }

        $user = Auth::user();

        $package = Package::create([
            'customer_id' => $user->id,
            'warehouse_id' => $request->warehouse_id,
            'pkg_type' => 'consolidation',
        ]);

        foreach ($request->package_consolidation as $key => $pkg) {
            $pkg = Package::find($pkg);
            $pkg->update([
                'package_handler_id' => $package->id,
                'pkg_type' => 'assigned',
            ]);
        }

        foreach ($package->child_packages as $child_pkg) {
            $service_requests = ServiceRequest::where('package_id', $child_pkg->id)->get();
            foreach ($service_requests as $key => $service_request) {
                ServiceRequest::create([
                    'service_id' => $service_request->service_id,
                    'package_id' => $package->id,
                    'child_package_id' => $service_request->package_id,
                    'price' => $service_request->price,
                    'status' => $service_request->status,
                    'admin_message' => $service_request->admin_message,
                    'customer_message' => $service_request->customer_message,
                ]);
            }
        }

        $users = User::where(['type' => 'admin'])->get();
        Notification::send($users, new CustomerPackageRequestNotification($package));

        return redirect()->route('packages.show', $package->id)->with('success', 'The package have consolidated successfully.');
    }

    public function multipiece(Request $request)
    {
        $query = Package::with('customer', 'warehouse')
            ->where('warehouse_id', $request->warehouse_id)
            ->where('status', 'open')
            ->where('pkg_type', 'single');

        if (Auth::user()->type == 'customer') {
            $query->where('customer_id', Auth::user()->id);
        }

        $packages = $query->orderBy('id', 'desc')->get();
        $warehouses = Warehouse::get();

        return Inertia::render('Packages/Multipiece', [
            'pkgs' => $packages,
            'warehouses' => $warehouses,
        ]);
    }

    public function storeMultipiece(Request $request)
    {
        if ($request->multipiece_package == []) {
            return redirect()->back()->with('error', 'Please select packages for multipiece.');
        }

        $user = Auth::user();

        $package = Package::with('order')->create([
            'customer_id' => $user->id,
            'warehouse_id' => $request->warehouse_id,
            'pkg_type' => 'multipiece',
            'admin_status' => 'accepted',
            'pkg_dim_status' => 'done',
        ]);

        foreach ($request->multipiece_package as $key => $pkg) {
            $pkg = Package::find($pkg);
            $pkg->update([
                'package_handler_id' => $package->id,
                'pkg_type' => 'assigned',
            ]);
        }


        foreach ($package->child_packages as $child_pkg) {
            PackageBox::create([
                'pkg_type' => 'multipiece',
                'package_id' => $package->id,
                'weight_unit' => $child_pkg->order->weight_unit,
                'dim_unit' => $child_pkg->order->dim_unit,
                'weight' => $child_pkg->order->package_weight,
                'length' => $child_pkg->order->package_length,
                'width' => $child_pkg->order->package_width,
                'height' => $child_pkg->order->package_height,
            ]);

            $service_requests = ServiceRequest::where('package_id', $child_pkg->id)->get();
            foreach ($service_requests as $key => $service_request) {
                ServiceRequest::create([
                    'service_id' => $service_request->service_id,
                    'package_id' => $package->id,
                    'child_package_id' => $service_request->package_id,
                    'price' => $service_request->price,
                    'status' => $service_request->status,
                    'admin_message' => $service_request->admin_message,
                    'customer_message' => $service_request->customer_message,
                ]);
            }
        }


        $users = User::where(['type' => 'admin'])->get();
        Notification::send($users, new CustomerPackageRequestNotification($package));

        return redirect()->route('packages.show', $package->id)->with('success', 'The package have multipiece successfully.');
    }

    public function updateAddress(Request $request)
    {
        $package = Package::find($request->package_id);
        $address = Address::find($request->address_book_id);
        if (isset($address)) {
            $address_type = $address->country_id == 226 ? 'domestic' : 'international';
            $package->update([
                'address_book_id' => $request->address_book_id,
                'address_type' => $address_type,
            ]);
        } else {
            $package->update([
                'address_book_id' => 0,
                'address_type' => NULL,
            ]);
        }

        return redirect()->route('packages.show', $package->id);
    }

    public function updateCharges(Request $request)
    {
        if ($request->type == 'service_request') {
            ServiceRequest::find($request->id)->update(['price' => $request->amount]);
        } else {
            $package = Package::find($request->package_id);

            if ($request->type == 'shipping_charges') {
                $package->update(['shipping_charges' => $request->amount]);
            }
        }

        return redirect()->back()->with('success', 'Charges Updated');
    }
}
