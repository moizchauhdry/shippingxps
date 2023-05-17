<?php

namespace App\Http\Controllers;

use App\Events\OrderChangesAcceptedByCustomerEvent;
use App\Events\OrderChangesByAdminEvent;
use App\Events\ShoppingCompletedEvent;
use App\Events\ShoppingCreatedEvent;
use App\Models\Coupon;
use App\Models\CustomerCoupon;
use App\Models\Order;
use App\Models\OrderComment;
use App\Models\OrderImage;
use App\Models\OrderItem;
use App\Models\SiteSetting;
use App\Models\Store;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\Package;
use App\Notifications\ShopForMeNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = Order::with(['customer', 'warehouse'])->whereIn('order_type', ['shopping', 'pickup']);

        $search = '';

        if (isset($_GET['search']) && !empty($_GET['search'])) {

            $search = $_GET['search'];
            $suite_number = intval($search) - 4000;
            $orders->join('users', 'orders.customer_id', '=', 'users.id');
            $orders->where(
                function ($query) use ($search, $suite_number) {
                    return $query->where('users.id', $suite_number)
                        ->orWhere('users.name', 'LIKE', "%$search%")
                        ->orWhere('orders.site_name', 'LIKE', "%$search%")
                        ->orWhere('orders.site_url', 'LIKE', "%$search%")
                        ->orWhere('orders.shipping_from_shop', 'LIKE', "%$search%");
                }
            );
        }

        if (auth()->user()->type === 'customer') {
            $orders->where('orders.customer_id', auth()->user()->id);
        }

        $orders->orderBy('orders.id', 'DESC');

        $orders = $orders->paginate(10);

        return Inertia::render('ShopForMe/OrdersList', [
            'search' => $search,
            'orders' => $orders
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $additional_pickup_charges = SiteSetting::where('name', 'additional_pickup_charges')->first()->value;
        $warehouses = Warehouse::all();
        $stores = Store::where('status', 1)->get();
        return Inertia::render('ShopForMe/CreateOrder', [
            'warehouses' => $warehouses,
            'stores' => $stores,
            'additional_pickup_charges' => $additional_pickup_charges,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'form_type' => 'required|in:shopping,pickup',
            'warehouse_id' => 'required',
            'notes' => 'nullable',
            'site_name' => 'required_if:form_type,shopping',
            'shop_url' => 'required_if:form_type,shopping',
            'store_id' => 'required_if:form_type,pickup',
            'pickup_type' => 'required_if:form_type,pickup',
            'pickup_date' => 'required_if:form_type,pickup',
            'shipping_from_shop' => 'required',
            'items' => 'array|required',
            'items.*.name' => 'required',
            'items.*.option' => 'required',
            'items.*.url' => 'required',
            'items.*.price' => 'required|numeric',
            'items.*.price_with_tax' => 'required|numeric',
            'items.*.qty' => 'required|numeric',
            'items.*.sub_total' => 'required|numeric',
        ], [
            'items.*.name.required' => 'The item name field is required.',
            'items.*.option.required' => 'The item description field is required.',
            'items.*.url.required' => 'The item URL field is required.',
            'items.*.price.required' => 'The item price field is required.',
            'items.*.price_with_tax.required' => 'The item price with tax field is required.',
            'items.*.qty.required' => 'The item quantity field is required.',
            'items.*.sub_total.required' => 'The item subtotal field is required.',
        ]);

        try {
            DB::beginTransaction();

            $order = new Order();
            $order->customer_id = auth()->user()->id;
            $order->warehouse_id = $request->warehouse_id;
            $order->package_id = 0;
            $order->status = 'pending';
            $order->notes = $request->notes;
            $order->discount = $request->discount;
            $order->store_charges = $request->store_charges ?? 0.00;
            $order->store_tax = $request->store_tax ?? 0.00;
            $order->sub_total = $request->sub_total;
            $order->grand_total = $request->grand_total;
            $order->service_charges = $request->service_charges;
            $order->shipping_from_shop = $request->shipping_from_shop;

            if ($request->form_type == 'shopping') {
                $order->order_type = 'shopping';
                $order->order_origin = 'shopping';
                $order->site_name = $request->site_name;
                $order->site_url = $request->shop_url;
                $order->pickup_date = null;
            } else {
                $order->order_type = 'pickup';
                $order->order_origin = 'pickup';
                $order->store_id = $request->store_id;
                $order->store_name = $request->store_name;
                $order->pickup_type = $request->pickup_type;
                $order->pickup_date = $request->pickup_date;
                $order->pickup_charges = $request->pickup_charges;
            }

            $order->save();

            foreach ($request->items as $item) {
                $order_item = new OrderItem();
                $order_item->order_id = $order->id;
                $order_item->name = $item['name'];
                $order_item->description = $item['option'];
                $order_item->quantity = $item['qty'];
                $order_item->unit_price = $item['price'];
                $order_item->price_with_tax = $item['price_with_tax'];
                $order_item->sub_total = $item['sub_total'];
                $order_item->url = $item['url'];
                $order_item->save();
            }

            $files = $request->file();

            if ($order->order_type == 'pickup') {
                if (isset($files['image'])) {
                    $image_object = $files['image'];
                    $file_name = time() . '_' . $image_object->getClientOriginalName();
                    $image_object->storeAs('uploads', $file_name);
                    if ($_SERVER['HTTP_HOST'] == 'localhost:8000') {
                        File::move(storage_path('app/uploads/' . $file_name), public_path('/public/uploads/' . $file_name));
                    } else {
                        File::move(storage_path('app/uploads/' . $file_name), public_path('../public/uploads/' . $file_name));
                    }
                    $order_image = new OrderImage();
                    $order_image->image = $file_name;
                    $order_image->order_id = $order->id;
                    $order_image->display = 1;
                    $order_image->display = 0;
                    $order_image->save();
                }
            }

            DB::commit();
            event(new ShoppingCreatedEvent($order));
            return redirect('shop-for-me')->with('success', 'Order Added!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Something went wrong.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $order = Order::with(['customer', 'images', 'items', 'store', 'warehouse', 'package'])->findOrFail($id);
        $additional_pickup_charges = SiteSetting::where('name', 'additional_pickup_charges')->first()->value;

        // echo '<pre>';
        // print_r($order);
        // exit; 

        return Inertia::render('ShopForMe/OrderDetail', [
            'order' => $order,
            'additional_pickup_charges' => $additional_pickup_charges,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $order = order::findOrFail($id);
        $additional_pickup_charges = SiteSetting::where('name', 'additional_pickup_charges')->first()->value;

        $items = [];

        $price = $order->items->sum('unit_price');
        $price_with_tax = $order->items->sum('price_with_tax');

        // foreach ($order->items as $item) {
        //     $items[] = [
        //         'id' => $item->id,
        //         'name' => $item->name,
        //         'description' => $item->description,
        //         'qty' => $item->quantity,
        //         'price' => $item->unit_price,
        //         'price_with_tax' => $item->price_with_tax,
        //         'sub_total' => $item->sub_total,
        //         'url' => $item->url
        //     ];
        // }

        $images = [];
        foreach ($order->images as $image) {
            $images[] = [
                'id' => $image->id,
                'image' => $image->image,
            ];
        }

        // $order = [
        //     'id' => $order->id,
        //     'warehouse_id' => $order->warehouse_id,
        //     'store_id' => $order->store_id,
        //     'store_charges' => $order->store_charges,
        //     'store_tax' => $order->store_tax,
        //     'site_name' => $order->site_name,
        //     'site_url' => $order->site_url,
        //     'status' => $order->status,
        //     'notes' => $order->notes,
        //     'shipping_from_shop' => $order->shipping_from_shop,
        //     'sales_tax' => $order->sales_tax,
        //     'order_type' => $order->order_type,
        //     'order_origin' => $order->order_origin,
        //     'only_pickup' => $order->only_pickup,
        //     'shipping_xps' => $order->shipping_xps_purchase,
        //     'pickup_date' => $order->pickup_date,
        //     'pickup_charges' => $order->pickup_charges,
        //     'pickup_type' => $order->pickup_type,
        //     'store_name' => $order->store_name,
        //     'sub_total' => $order->sub_total,
        //     'grand_total' => $order->grand_total,
        //     'service_charges' => $order->service_charges,
        //     'is_service_charges' => $order->is_service_charges,
        //     'discount' => $order->discount,
        //     'receipt_url' => $order->receipt_url,
        //     'payment_status' => $order->payment_status,
        //     'image' => '',
        //     'items' => $items,
        //     'images' => $images,
        // ];

        if (Auth::user()->type != 'admin') {
            $order['is_changed'] = $order->is_changed;
            $order['updated_by_admin'] = $order->updated_by_admin;
            $order['changes_approved'] = $order->changes_approved;
        } else {
            $order['changes_approved'] = $order->changes_approved;
        }

        $warehouses = Warehouse::select(['id', 'name', 'sale_tax'])->get()->toArray();
        $stores = Store::select(['id', 'name'])->get()->toArray();



        $comments = OrderComment::where('order_id', $order->id)->with('user')->orderBy('id', 'desc')->get();

        return Inertia::render('ShopForMe/EditOrder', [
            'order' => $order,
            'warehouses' => $warehouses,
            'stores' => $stores,
            'salePrice' => (int)$price_with_tax - $price,
            'additional_pickup_charges' => $additional_pickup_charges,
            'comments' => $comments
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateOrder(Request $request)
    {
        // dd($request->all());

        $id = $request->input('id');
        $order = Order::findOrFail($id);

        $rules = [
            'form_type' => 'required|in:shopping,pickup',
            'warehouse_id' => 'required',
            'notes' => 'nullable',
            'site_name' => 'required_if:form_type,shopping',
            'shop_url' => 'required_if:form_type,shopping',
            'store_id' => 'required_if:form_type,pickup',
            'pickup_type' => 'required_if:form_type,pickup',
            'pickup_date' => 'required_if:form_type,pickup',
            'shipping_from_shop' => 'required',
            'items' => 'array|required',
            'items.*.name' => 'required',
            'items.*.description' => 'required',
            'items.*.url' => 'required',
            'items.*.unit_price' => 'required|numeric',
            'items.*.price_with_tax' => 'required|numeric',
            'items.*.quantity' => 'required|numeric',
            'items.*.sub_total' => 'required|numeric',
        ];

        $validator = Validator::make($request->all(), $rules, $message = [
            'receipt_url.required_if' => 'The image of an order is missing',
            'items.*.name.required' => 'The item name field is required.',
            'items.*.option.required' => 'The item description field is required.',
            'items.*.url.required' => 'The item URL field is required.',
            'items.*.price.required' => 'The item price field is required.',
            'items.*.price_with_tax.required' => 'The item price with tax field is required.',
            'items.*.qty.required' => 'The item quantity field is required.',
            'items.*.sub_total.required' => 'The item subtotal field is required.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        $isAdmin = (Auth::user()->type == 'admin') ? true : false;

        try {
            DB::beginTransaction();

            $order->warehouse_id = $request->warehouse_id;
            $order->notes = $request->notes;

            if (!$isAdmin && $request->has('changes_approved')) {
                $order->changes_approved = $request->changes_approved;
                $order->updated_by_admin = false;
                $order->is_changed = false;
            }

            if (isset($request->shipping_from_shop)) {
                $order->shipping_from_shop = $request->shipping_from_shop;
            }
            if (isset($request->sales_tax)) {
                $order->sales_tax = $request->sales_tax;
            }

            if ($request->form_type == 'shopping') {
                $order->order_type = 'shopping';
                $order->order_origin = 'shopping';
                $order->site_name = $request->site_name;
                $order->site_url = $request->shop_url;
                $order->pickup_date = null;
            } else {
                $order->order_type = 'pickup';
                $order->order_origin = 'pickup';
                $order->store_id = $request->store_id;
                $order->store_name = $request->store_name;
                $order->pickup_type = $request->pickup_type;
                $order->pickup_date = $request->pickup_date;
                $order->pickup_charges = $request->pickup_charges;
            }

            $order->sub_total = $request->sub_total;
            $order->grand_total = $request->grand_total;
            $order->service_charges = $request->service_charges;
            $order->is_service_charges = $request->is_service_charges;
            $order->discount = $request->discount;

            if ($request->is_complete_shopping == 1) {

                $package = Package::where([
                    'status' => 'open',
                    'customer_id' => $order->customer_id,
                    'warehouse_id' => $request->input('warehouse_id')
                ])->get()->first();


                if (!is_object($package)) {

                    $count = Package::where([
                        'customer_id' => $order->customer_id,
                    ])->count();

                    $next = (int) $count + 1;
                    $package = new Package();

                    $package->package_no = 'Pkg-' . $order->customer_id . '-' . $next;
                    $package->customer_id = $order->customer_id;
                    $package->warehouse_id =  $request->input('warehouse_id');
                    $package->status = 'open';
                    $package->package_handler_id = ($isAdmin) ? Auth::user()->id : null;
                    $package->save();
                }

                if ($order->order_origin == 'shopping') {
                    $order->received_from = $order->site_name;
                } else if ($order->order_origin == 'pickup') {
                    $order->received_from = $order->store->name;
                }

                $order->package_id = $package->id;
                $order->order_type = 'order';
                $order->status = 'arrived';
                $order->arrived_at = Carbon::now();
                $order->save();

                event(new ShoppingCompletedEvent($order));
            }

            $order->save();

            $checkChanges = $order->wasChanged();
            $orderItemChanges[] = [];
            OrderItem::where('order_id', $order->id)->delete();
            foreach ($request->items as $item) {
                $order_item = OrderItem::create([
                    'order_id' => $order->id,
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'url' => $item['url'],
                    'unit_price' => $item['unit_price'],
                    'price_with_tax' => $item['price_with_tax'],
                    'sub_total' => $item['sub_total'],
                ]);
                $orderItemChanges[] = $order_item->wasChanged();
            }

            if ($request->is_complete_shopping != 1 && $isAdmin) {
                $order->updated_by_admin = true;
                $order->changes_approved = false;
                if ($checkChanges != null || $orderItemChanges != null) {
                    $order->is_changed = true;
                    $order->save();
                }
                event(new OrderChangesByAdminEvent($order));
            }

            if (!$isAdmin && $request->has('changes_approved')) {
                event(new OrderChangesAcceptedByCustomerEvent($order));
            }

            if ($request->form_type == 'pickup') {
                $files = $request->file();
                if (isset($files['image'])) {
                    $image_object = $files['image'];
                    $file_name = time() . '_' . $image_object->getClientOriginalName();
                    $image_object->storeAs('uploads', $file_name);

                    if ($_SERVER['HTTP_HOST'] == 'localhost:8000') {
                        File::move(storage_path('app/uploads/' . $file_name), public_path('/uploads/' . $file_name));
                    } else {
                        File::move(storage_path('app/uploads/' . $file_name), public_path('../uploads/' . $file_name));
                    }

                    $order_image = new OrderImage();
                    $order_image->image = $file_name;
                    $order_image->order_id = $order->id;
                    $order_image->display = 1;
                    $order_image->display = 0;
                    $order_image->save();
                }
            }

            DB::commit();
            if (!$isAdmin && $request->has('changes_approved') && $request->get('changes_approved') == 1) {
                return redirect()->route('payment.index', [
                    'payment_module' => 'order',
                    'payment_module_id' => $order->id,
                ]);
            } else {
                return redirect('shop-for-me')->with('success', 'Order Updated !');
            }
        } catch (\Exception $e) {
            return redirect('shop-for-me')->with('error', 'Something went wrong: ' . $e->getMessage());
            DB::rollBack();
        }
    }


    public function filterStores($id)
    {
        $stores = Store::where('warehouse_id', $id)->where('status', 1)->get();
        return response()->json([
            'status' => 200,
            'stores' => $stores
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /* Coupon Mangement */
    public function checkCoupon(Request $request)
    {
        // dd($request->code);
        $customer = Auth::user();
        $code = $request->code;

        $coupon = Coupon::where('code', $code)->first();

        if ($coupon != null) {
            $strCheck = strcmp($code, $coupon->code);
            if ($strCheck == 0) {
                $checkCode = CustomerCoupon::where('coupon_id', $coupon->id)->where('customer_id', $customer->id)->get();
                if ($checkCode->count() > 0) {
                    return response()->json([
                        'status' => 0,
                        'message' => 'Coupon already used',
                    ]);
                } else {
                    return response()->json([
                        'status' => 1,
                        'message' => 'Coupon Applied',
                        'discount' => $coupon->discount,
                    ]);
                }
            } else {
                return response()->json([
                    'status' => 0,
                    'message' => 'Coupon is invalid',
                ]);
            }
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Coupon is invalid',
            ]);
        }
    }

    public function storeComment(Request $request, $id)
    {
        $user = Auth::user();
        $shopForMe = Order::find($id);
        $validatedData = $request->validate([
            'message' => 'required',
        ]);

        $order_comment = OrderComment::create([
            'order_id' => $id,
            'user_id' => $user->id,
            'message' => $validatedData['message'],
        ]);

        $url = URL::route('shop-for-me.edit', $shopForMe->id);
        $auser = $user->type == 'admin' ? 'Admin' : $order_comment->user->name;
        $data = [
            'url' => URL::route('additional-request.edit', $shopForMe->id),
            'message' => $auser . ' has commented on an shopping list. <a style="font-weight:600" href="' . $url . '">Click Here</a>',
        ];
        if ($user->type == 'admin') {
            $customer = User::find($shopForMe->customer_id);
            $customer->notify(new ShopForMeNotification($data));
        } else {
            $admins = User::where('type', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new ShopForMeNotification($data));
            }
        }
        $shopForMe = Order::find($id);

        return redirect()->back();
    }

    public function loadComments($id)
    {
        $comments = OrderComment::where('order_id', $id)->get();
        return response()->json([
            'comments' => $comments
        ]);
    }

    public function changeStatus(Request $request)
    {
        $id = $request->id;
        $status = $request->status;

        $order = Order::find($id);
        $order->status = $status;
        $order->save();

        return redirect()->back();
    }

    public function updateInvoice(Request $request)
    {
        // dd($request->all());

        $order = Order::find($request->order_id);

        $files = $request->file();
        if (isset($files['receipt_url'])) {
            $image_object = $files['receipt_url'];
            $file_name = time() . '_' . $image_object->getClientOriginalName();
            $image_object->storeAs('uploads', $file_name);
            if ($_SERVER['HTTP_HOST'] == 'localhost:8000') {
                File::move(storage_path('app/uploads/' . $file_name), public_path('/public/uploads/' . $file_name));
            } else {
                File::move(storage_path('app/uploads/' . $file_name), public_path('../public/uploads/' . $file_name));
            }
            $order->update([
                'receipt_url' => $file_name,
            ]);
        }

        $order->update([
            'tracking_number_in' => $request->tracking_number_in,
        ]);

        return redirect()->back()->with('success', 'Invoice and Tracking Number have been update successfully!');
    }
}
