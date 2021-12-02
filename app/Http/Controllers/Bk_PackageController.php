<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use \Illuminate\Http\Response;
use Inertia\Inertia;
use GuzzleHttp\Client;
use App\Models\Order;
use App\Models\Country;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Auth;
use File;


class PackageController extends Controller
{
    public $status_list = ['arrived','labeled','shipped','delivered','rejected'];
    
    public function index(){
        
        $customer_id =Auth::user()->id;

        $arrived_count = Order::where('status','=','arrived')
                        ->where('customer_id','=',$customer_id)
                        ->count();

        $arrived = Order::with('displayImage')
                    ->where('status','=','arrived')
                    ->where('customer_id','=',$customer_id)
                    ->get();
        

        $labeled_count = Order::where('status','=','labeled')
                    ->where('customer_id','=',$customer_id)
                    ->count();

        $labeled = Order::where('status','=','labeled')
                    ->where('customer_id','=',$customer_id)
                    ->get();

        $shipped_count = Order::where('status','=','shipped')
                    ->where('customer_id','=',$customer_id)
                    ->count();                    
  
        $shipped = Order::where('status','=','shipped')
                    ->where('customer_id','=',$customer_id)
                    ->get();

        $rejected_count = Order::where('status','=','rejected')
                    ->where('customer_id','=',$customer_id)
                    ->count();

        $rejected = Order::where('status','=','rejected')
                    ->where('customer_id','=',$customer_id)
                    ->get();
  
        return Inertia::render('Packages/List',[
          'arrived_count' => $arrived_count,
          'arrived' => $arrived,
          'labeled_count' => $labeled_count,
          'labeled' => $labeled,
          'shipped_count' => $shipped_count,
          'shipped' => $shipped,
          'rejected_count' => $rejected_count,
          'rejected' => $rejected
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
       
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $order_id)
    {  
        
        $user_id =Auth::user()->id;

        $order = Order::with(['items','images'])->find($order_id); 
        $items = [];

        foreach($order->items as $item){
            $items[] = [
                'description' => $item->description,
                'quantity' => $item->quantity,
                'value' => 0,
                'origin_country' => 0,
                'batteries' => 0,
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

        return Inertia::render('Packages/CreatePackage',[
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
    public function store(Request $request)
    {

      $order = Order::find($request->input('order_id'));

      $validated = $request->validate([
          'address_book_id' => 'required'
      ]);

      $customer = User::find($order->customer_id);

      $package = new Order();
      $package->order_type = 'package';
      $package->status = 'labeled';
      $package->order_id = $order->order_id;
      $package->tracking_number_in = $order->tracking_number_in;

      $package->customer_id = $order->customer_id;
      $package->shipping_total = $order->shipping_total;
      
      $package->package_weight = $order->package_weight;

      //package
      $package->package_weight = $order->package_weight;
      $package->package_length = $order->package_length;
      $package->package_width = $order->package_width;
      $package->package_height = $order->package_height;
      $package->declared_value =  $order->declared_value;
      $package->address_book_id =  $order->address_book_id;

      $package->sender_fullname = "Shipping Xps";
      $package->sender_address = "4 TAFT RD, SUITE #1001 , NEW JERSEY, US";
      $package->sender_email = "shippingxps@gmail.com";
      $package->sender_phone = "+123-1234-567";

      //receiver
      $package->receiver_fullname = $customer->first_name.' '.$customer->last_name;
      $package->receiver_address = $customer->address1;
      $package->receiver_email = $customer->email;
      $package->receiver_phone = $customer->phone_no;

      $package->notes = $order->notes;

      $package->save();
      
      $order->status = 'labeled';
      $order->update();

      $batteries = [
          0 => 'No Batteries',
          1 => 'Simple Batteries (Shipped on on Fedex)',
          2=> 'Batteries Packaed with Equipment',
          3=> 'Batteries Contained in Equipment'
      ];

      $items = $request->input('items');

      foreach($items as $key => $item){

          $order_item = new OrderItem();
          $order_item->order_id = $package->id;
          $order_item->name = $item['description'];
          $order_item->description = $item['description'];
          $order_item->quantity = $item['quantity'];
          $order_item->unit_price = $item['value'];
          $order_item->origin_country = $item['origin_country'];
          $order_item->batteries = $item['batteries'];
          $order_item->save();
      }

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

 
        $id = $request->input('id');

        $package = Order::find($id);

        $validated = $request->validate([
            'address_book_id' => 'required'
        ]);

        $package->address_book_id = $validated['address_book_id'];


        $package->update();

        //$order->items()->delete();

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

        $order = Order::with('items')->find($order_id);

        $user = User::find($order->customer_id);

        $address = Address::find($order->address_book_id);

        $html = '';

        $html .= '<h1 style="text-align:center;"> Invoice </h1>';
        $html .= '<table  style="width:100%;margin-top:30px;">';

        $html .= '<tr>';
        $html .= '<td cellspacing="0" width="50%" style="height: 80px; border:1px solid #c1c1c1; margin:0;">
                    Ship From : <br>

                    </td>';
        $html .= '<td cellspacing="0" width="50%" style="height: 80px; border:1px solid #c1c1c1; margin:0;">
                    Tracking Number IN: '.$order->tracking_number_in.' <br>
                    Tracking Number OUT: '.$order->tracking_number_out.'
                </td>';
        $html .= '</tr>';

        $html .= '<tr>';
        if(is_object($address)){
            $html .= '<td cellspacing="0" width="50%" style="height: 120px; border:1px solid #c1c1c1; margin:0; padding:5px;">
            Ship To : <br>
            PHone : '.$address->phone.'<br>
            Email : '.$user->email.'
            City : '.$address->city.'
            State : '.$address->state.'
            Address : '.$address->address.'
            Country : '.$address->Country.'
            </td>';
        }else{
            $html .= '<td cellspacing="0" width="50%" style="height: 120px; border:1px solid #c1c1c1; margin:0; padding:5px;">
            Ship To : <br>
            PHone : 4541545454<br>
            Email : '.$user->email.'
            City : Dubai    <br>
            State : Dubai<br>
            Address : State building floor 23, Flat 23<br>
            Country : United Arab<br>
            </td>';
        }

        $html .= '<td cellspacing="0" width="50%" style="height: 120px; border:1px solid #c1c1c1; margin:0;">
                    Sold TO : Same as shipped to     
                </td>';
        $html .= '</tr>';

        $html .= '</table>'; 

        $html .= '<h1> Items </h1>';

        $html .= '<table  style="width:100%;">';

        $html .= '<tr>';
        $html .= '<td cellspacing="0" style="border-bottom:1px solid #c1c1c1; margin:0;">Qty</td>';
        $html .= '<td cellspacing="0" style="border-bottom:1px solid #c1c1c1; margin:0;">Unit</td>';
        $html .= '<td cellspacing="0" style="border-bottom:1px solid #c1c1c1; margin:0;">Description </td>';
        $html .= '<td cellspacing="0" style="border-bottom:1px solid #c1c1c1; margin:0;">Origin</td>';
        $html .= '<td cellspacing="0" style="border-bottom:1px solid #c1c1c1; margin:0;">Price</td>';
        $html .= '<td cellspacing="0" style="border-bottom:1px solid #c1c1c1; margin:0;">Total(USD)</td>';
        $html .= '</tr>';

        foreach($order->items as $item){

            $country = Country::find($item->origin_country);


            $html .= '<tr>';

            $html .= '<td cellspacing="0" style="border-bottom:1px solid #c1c1c1; margin:0;">'.$item->quantity.'</td>';
            $html .= '<td cellspacing="0" style="border-bottom:1px solid #c1c1c1; margin:0;">PCS</td>';
            $html .= '<td cellspacing="0" style="border-bottom:1px solid #c1c1c1; margin:0;">'.$item->description.'</td>';
            $html .= '<td cellspacing="0" style="border-bottom:1px solid #c1c1c1; margin:0;">'.$country->nicename.'</td>';
            $html .= '<td cellspacing="0" style="border-bottom:1px solid #c1c1c1; margin:0;">'.$item->unit_price.'</td>';
            $html .= '<td cellspacing="0" style="border-bottom:1px solid #c1c1c1; margin:0;">'.$item->unit_price*$item->quantity.'</td>';

            $html .= '</tr>';
        }

        $html .= '</table>';

        $mpdf = new \Mpdf\Mpdf();
        $mpdf->WriteHTML($html);
        $mpdf->Output();


    }

    public function removeItem(Request $request){

        $item_id = $request->input('item_id');
        OrderItem::find($item_id)->delete();
    }

    public function getStorageFee($id)
    {

    }
    
}