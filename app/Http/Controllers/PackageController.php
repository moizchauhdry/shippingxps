<?php

namespace App\Http\Controllers;

use App\Events\CustomFormFilled;
use App\Events\PackageConsolidated;
use App\Events\PackageShipped;
use App\Events\PackageShippingServiceSelected;
use App\Events\ServiceRequestedEvent;
use App\Events\ServiceRequestUpdatedEvent;
use App\Models\Address;
use Illuminate\Http\Request;
use \Illuminate\Http\Response;
use Inertia\Inertia;
use GuzzleHttp\Client;
use App\Models\Order;
use App\Models\Package;
use App\Models\Country;
use App\Models\OrderItem;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Models\SiteSetting;
use App\Models\Shipping;
use Illuminate\Support\Facades\Auth;
use File;


class PackageController extends Controller
{
    public $status_list = ['arrived','labeled','shipped','delivered','rejected'];
    
    public function index(){
        
        $user_type = Auth::user()->type;

        if($user_type == 'customer'){
           return $this->customer_packages();
        }else{

            $query = Package::with(['warehouse','customer','orders']);
            $packages = $query->paginate(25);
            
            return Inertia::render('Packages/AdminPackageList',[
              'packages' => $packages
            ]); 
        }

    }

    public function customer_packages(){

        $customer_id =Auth::user()->id;

        $query = Package::with(['warehouse','customer','orders']);
        $query->where('customer_id','=',$customer_id);
        $query->whereIn('status',['open','filled','consolidated']);
        $packages_account = $query->paginate(25);

        $query_ready = Package::with(['warehouse','customer','orders']);
        $query_ready->where('customer_id','=',$customer_id);
        $query_ready->whereIn('status',['labeled']);
        $packages_ready = $query_ready->paginate(25);

        $query_sent = Package::with(['warehouse','customer','orders']);
        $query_sent->where('customer_id','=',$customer_id);
        $query_sent->whereIn('status',['shipped','complete','delivered']);
        $packages_sent = $query_sent->paginate(25);

        return Inertia::render('Packages/CustomerPackageList',[
          'packages_account' => $packages_account,
          'packages_ready' => $packages_ready,
          'packages_sent' => $packages_sent,
        ]); 
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $packag = Package::with(['orders','address','warehouse','customer','items','images','serviceRequests'])->find($id);

        $services = Service::where('status','=','1')->get();

        $service_requests = [];
        foreach($packag->serviceRequests as $req){
            $service_requests[] = [
                'id' => $req->id,
                'service_title' => $req->service->title,
                'service_description' => $req->service->description,
                'status' => $req->status,
                'customer_message' => $req->customer_message,
                'admin_message' => $req->admin_message,
                'price'=> $req->price,
                'date' => $req->created_at,                
            ];
        }

        $images = [];
        foreach($packag->images as $image){
            $images[] = [
                'order_id'=> $image->order_id,
                'img_url' => $image->image,                
            ];
        } 

        $pickup_service_charges = SiteSetting::getByName('pickup_service_charges');
        $order_charges = [];
        foreach($packag->orders as $order){

            if($order->order_origin == 'pickup'){                
                
                $total = 0;
                foreach($order->items as $item){
                    $total = $item->unit_price * $item->quantity;
                }
                //add sales tax
                $sales_tax = ($order->sales_tax/100)*$total;
                $sub_total = $total + $sales_tax;
                $service_charges = ($pickup_service_charges/100)*$sub_total;
                $order_charges[] = [
                    'total' => $total,
                    'sales_tax' => $sales_tax,
                    'sub_total' => $sub_total,
                    'service_charges' => $service_charges
                ];
            }
        }

        return Inertia::render('Packages/PackageDetails',[
          'packag' => $packag,
          'services' => $services,
          'service_requests' => $service_requests,
          'images' => $images,
          'order_charges' => $order_charges,
          'mailout_fee' => (int) SiteSetting::getByName('mailout_fee'),
          'shipping_services' => Shipping::getShippingServices(),
        ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function custom(Request $request, $package_id)
    {  
        
        $user_id =Auth::user()->id;

        $packag = Package::with(['orders','warehouse','customer','items'])->find($package_id);

        $warehouse = $packag->warehouse;

        $items = [];

        foreach($packag->items as $item){
            $items[] = [
                'id' => $item->id,
                'description' => $item->description,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'origin_country' => $item->origin_country,
                'batteries' => $item->batteries,
            ];

        }

        $tracking_numbers = [];

        foreach($packag->orders as $order){
            $tracking_numbers[] = $order->tracking_number_in;
        }
            
        $addresses = Address::where('user_id',$user_id)->get();

        $address_book = [];

        $selected_address = '';
        $address_book_id = 0;
        
        foreach($addresses as $address){
            
            $full_address = $address->fullname." ".$address->address."<br>".$address->city." ".$address->state." ".$address->country->nicename."<br>".$address->phone;
            
            if($selected_address == ''){
                $selected_address = $full_address;
                $address_book_id = $address->id;
            }

            $address_book[$address->id] = [
                'id' => $address->id,
                'label' => $address->fullname." ".$address->city." ".$address->state,
                'full_address' => $full_address
            ];
        }

        $countries = Country::all(['id','nicename as name'])->toArray();

        return Inertia::render('Packages/CreatePackage',[
          'countries' => $countries,
          'items' => $items,
          'address_book' => $address_book,
          'address_book_id' => $address_book_id,
          'selected_address' => $selected_address,
          'packag'=> $packag,
          'warehouse' => $warehouse,
          'tracking_numbers' => $tracking_numbers,
          'package_date' => date('Y-m-d')
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

      $package = Package::find($request->input('package_id'));

      $validated = $request->validate([
          'address_book_id' => 'required',
          'shipping_total' => 'required',
          'package_type' => 'required'
      ]);

      //$customer = User::find($package->customer_id);

      $package->status = 'filled';
      $package->shipping_total = $validated['shipping_total'];
      $package->address_book_id = $validated['address_book_id'];
      $package->package_type = $validated['package_type'];

      $package->save();

      //if package has just 1 order, set package dimentions as order dimentions.
      $orders = $package->orders;

    //   echo '<pre>';
    //   print_r($orders);
    //   exit; 

      if(count($orders) ==1){
          $order = $orders[0];

          $package->package_weight = $order->package_weight;
          $package->weight_unit = $order->weight_unit;
          $package->package_length = $order->package_length;
          $package->package_width = $order->package_width;
          $package->package_height = $order->package_height;
          $package->dim_unit = $order->dim_unit;
          $package->save();
      }
      

      $items = $request->input('items');

      foreach($items as $key => $item){
          
          if(isset($item['id'])){
              $order_item = OrderItem::find($item['id']);
          }else{
              //problem here, which order to assign this item id. 
             $order_item = new OrderItem();
          }
          
          $order_item->name = $item['description'];
          $order_item->description = $item['description'];
          $order_item->quantity = $item['quantity'];
          $order_item->unit_price = $item['unit_price'];
          $order_item->origin_country = $item['origin_country'];
          $order_item->batteries = $item['batteries'];
          $order_item->save();
      }

      event(new CustomFormFilled($package));

      return redirect('packages')->with('success', 'Package updated!');

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
        $items = [];

        $user_id =Auth::user()->id;

        $order = Order::with(['items','images'])->find($id); 
        $items = [];

        foreach($order->items as $item){
            $items[] = [
                'id' => $item->id,
                'description' => $item->description,
                'quantity' => $item->quantity,
                'value' => $item->unit_price,
                'origin_country' =>$item->origin_country,
                'batteries' => $item->batteries,
            ];
        }
            
        $addresses = Address::where('user_id',$user_id)->get();

        $address_book = [];

        $first_address = '';
        $address_book_id = 0;
        
        foreach($addresses as $address){
            
            $full_address = $address->fullname." ".$address->address."<br>".$address->city." ".$address->state." ".$address->country."<br>".$address->phone;
            if($first_address == ''){
                $first_address = $full_address;
                $address_book_id = $address->id;
            }

            $address_book[$address->id] = [
                'id' => $address->id,
                'label' => $address->fullname." ".$address->city." ".$address->state,
                'full_address' => $full_address
            ];
        }

        $countries = Country::all(['id','nicename as name'])->toArray();

        return Inertia::render('Packages/EditPackage',[
          'countries' => $countries,
          'items' => $items,
          'address_book' => $address_book,
          'address_book_id' => $address_book_id,
          'first_address' => $first_address,
          'order'=> $order
        ]);  

    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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
            2=> 'Batteries Packaed with Equipment',
            3=> 'Batteries Contained in Equipment'
        ];
  

        foreach($items as $key => $item){

            $item_id = isset($item['id']) ? (int) $item['id'] : 0;

            $order_item = OrderItem::find($item_id);
            //update if existing, else creat new. 

            if(!is_object($order_item)){
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


    public function getPdf(Request $request, $order_id){

        $package = Package::with(['items','warehouse.country'])->find($order_id);

        $warehouse = $package->warehouse;

        $user = User::find($package->customer_id);

        $address = Address::find($package->address_book_id);

        $html = view('pdfs.invoice',[
            'package' => $package,
            'warehouse' => $warehouse,
            'user' => $user,
            'address' => $address
        ])->render();

        //echo $html; exit;
       
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

    /**
     * Create service request against package by customer
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function serviceRequest(Request $request){

        $service = $request->input('service');      
        $service_request = new ServiceRequest();
        $service_request->service_id = $service['id'];
        $service_request->package_id = $request->input('package_id');
        $service_request->price = $service['price'];
        $service_request->status = 'pending';
        $service_request->customer_message = $request->input('customer_message');
        $service_request->save();

        if($service['keyword']=='consolidation'){
            $package = Package::find($request->input('package_id'));
            $package->consolidation_request = 1;
            $package->update();
        }

        event(new ServiceRequestedEvent($service_request));
        
        //return redirect()->route('packages.show',['id'=>$request->input('package_id')])->with('success', 'Your service request sent to Admin.');
        return redirect()->back()->with('success', 'Your service request sent to Admin.');
    }


    /**
     * Create service request against package by customer
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function serviceHandle(Request $request){

        $service_request_data = $request->input('request');

        $service_reqeust = ServiceRequest::find($service_request_data['id']);
        $service_reqeust->status = $request->input('status');
        $service_reqeust->admin_message = $request->input('admin_message');
        $service_reqeust->price = $service_request_data['price'];
        $service_reqeust->save();

        event(new ServiceRequestUpdatedEvent($service_reqeust));

        return redirect()->back()->with('success', 'Service request updated, customer notfied.');

    }


    /**
     * Create service request against package by customer
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function consolidatePackage(Request $request){

        $data = $request->all();

        $package = Package::find($data['package_id']);
        $package->status = $data['status'];
        $package->weight_unit = $data['weight_unit'];
        $package->dim_unit = $data['dim_unit'];
        $package->package_weight = $data['package_weight'];
        $package->package_length = $data['package_length'];
        $package->package_width = $data['package_width'];
        $package->package_height = $data['package_height'];
        $package->update();

        event(new PackageConsolidated($package));

        return redirect()->back()->with('success', 'Package set for shipment.');

    }


        /**
     * Create service request against package by customer
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function shipPackage(Request $request){

        $data = $request->all();

        $package = Package::find($data['package_id']);
        $package->status = $data['status'];
        $package->tracking_number_out = $data['tracking_number_out'];
        $package->update();

        event(new PackageShipped($package));
        
        return redirect()->back()->with('success', 'Package set for shipment.');

    }


        /**
     * Create service request against package by customer
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function setShippingService(Request $request){

        $data = $request->all();

        $service = $data['service'];

        $package = Package::find($data['package_id']);
        $package->status = $data['status'];
        $package->carrier_code = $service['carrierCode'];
        $package->service_label = $service['serviceLabel'];
        $package->service_code = $service['serviceCode'];
        $package->package_type_code = $service['packageTypeCode'];
        $package->currency = $service['currency'];
        $package->shipping_total = $service['totalAmount'];
        $package->update();

        event(new PackageShippingServiceSelected($package));

        return redirect()->back()->with('success', 'Package set for shipment.');

    }



    public function removeItem(Request $request){

        // $item_id = $request->input('item_id');
        // OrderItem::find($item_id)->delete();
    }
    
}