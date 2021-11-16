<?php

namespace App\Http\Controllers;

use App\Events\OrderChangesAcceptedByCustomerEvent;
use App\Events\OrderChangesByAdminEvent;
use App\Events\ShoppingCompletedEvent;
use App\Events\ShoppingCreatedEvent;
use App\Models\Coupon;
use App\Models\CustomerCoupon;
use App\Models\Order;
use App\Models\OrderImage;
use App\Models\OrderItem;
use App\Models\Store;
use App\Models\Warehouse;
use App\Models\Package;
use App\Notifications\ShoppingCreated;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use File;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        
        $orders = Order::with(['customer','warehouse'])->whereIn('order_type', ['shopping', 'pickup']);

        $search = '';

        if(isset($_GET['search']) && !empty($_GET['search'])){

            $search = $_GET['search'];

            $orders->where(
                function($query) use ($search) {

                    return $query
                        ->orWhere('site_name', 'LIKE', "%$search%")
                        ->orWhere('site_url', 'LIKE', "%$search%")
                        ->orWhere('shipping_from_shop', 'LIKE', "%$search%");
                }
            );
        }

        if(auth()->user()->type === 'customer') {
            $orders->where('customer_id', auth()->user()->id);
        }

        $orders->orderBy('id', 'DESC');

        $orders = $orders->paginate(25);

        return Inertia::render('ShopForMe/OrdersList',[
            'search' => $search,
            'orders' => $orders
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $warehouses = Warehouse::all();
        $stores = Store::all();
        return Inertia::render('ShopForMe/CreateOrder',[
            'warehouses' => $warehouses,
            'stores' => $stores
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
//         dd($request->all());
        $request->validate([
            'form_type' => 'required|in:shopping,pickup',
            'warehouse_id' => 'required',
            'notes' => 'nullable|string',
            'items' => 'required',
            'site_name' => 'required_if:form_type,shopping',
            'shop_url' => 'required_if:form_type,shopping',
            'store_id' => 'required_if:form_type,pickup',
            'pickup_type' => 'required_if:form_type,pickup',
            'pickup_date' => 'required_if:form_type,pickup'
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
            $order->sub_total = $request->sub_total;
            $order->grand_total = $request->grand_total;
            $order->service_charges = $request->service_charges;
            //$order->shipping_from_shop = $request->shipping_from_shop;
            //$order->sales_tax = $request->sales_tax;

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
                //$order->shipping_xps_purchase = $request->shipping_xps;
                $order->pickup_date = $request->pickup_date;
                $order->pickup_charges = $request->pickup_charges;
            }

            $order->save();

            /*$coupon = Coupon::where('code',$request->code)->first();
            $customerCoupon = CustomerCoupon::create([
                'customer_id' => Auth::user()->id,
                'coupon_id' => $coupon->id,
            ]);*/

            foreach($request->items as $item){

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

            if($order->order_type == 'pickup'){

                if(isset($files['image'])){
                    
                    $image_object = $files['image'];

                    $file_name = time().'_'.$image_object->getClientOriginalName();
                    $image_object->storeAs('uploads', $file_name);

                    if($_SERVER['HTTP_HOST'] == 'localhost:8000'){
                        File::move(storage_path('app/uploads/'.$file_name), public_path('/public/uploads/'.$file_name) );
                    }else{
                        File::move(storage_path('app/uploads/'.$file_name), public_path('../public/uploads/'.$file_name) );
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
            dd($e);
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
    public function show($id) {

        $order = Order::with(['customer','images','items','store','warehouse','package'])->findOrFail($id);

        // echo '<pre>';
        // print_r($order);
        // exit; 

        return Inertia::render('ShopForMe/OrderDetail',[
            'order' => $order,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        $model = order::find($id);

        $items = [];

        $price = $model->items->sum('unit_price');
        $price_with_tax = $model->items->sum('price_with_tax');

        foreach($model->items as $item){
            $items[] = [
                'id' => $item->id,
                'name' => $item->name,
                'description' => $item->description,
                'qty' => $item->quantity,
                'price' => $item->unit_price,
                'price_with_tax' => $item->price_with_tax,
                'sub_total' => $item->sub_total,
                'url' => $item->url
            ];
        }

        $images = [];

        foreach($model->images as $image){
            $images[] = [
                'id' => $image->id,
                'image' => $image->image,
            ];
        }


        $order = [
            'id' => $model->id,
            'warehouse_id' => $model->warehouse_id,
            'store_id' => $model->store_id,
            'site_name' => $model->site_name,
            'site_url' => $model->site_url,
            'status' => $model->status,
            'notes' => $model->notes,
            'shipping_from_shop' => $model->shipping_from_shop,
            'sales_tax' => $model->sales_tax,
            'order_type' => $model->order_type,
            'order_origin' => $model->order_origin,
            'only_pickup' => $model->only_pickup,
            'shipping_xps' => $model->shipping_xps_purchase,
            'pickup_date' => $model->pickup_date,
            'pickup_charges' => $model->pickup_charges,
            'pickup_type' => $model->pickup_type,
            'store_name'=>$model->store_name,
            'sub_total'=>$model->sub_total,
            'grand_total'=>$model->grand_total,
            'service_charges'=>$model->service_charges,
            'is_service_charges'=>$model->is_service_charges,
            'discount'=>$model->discount,
            'receipt_url'=>$model->receipt_url,
            'image' => '',
            'items' => $items,
            'images' => $images,
        ];

        if(Auth::user()->type != 'admin'){
            $order['is_changed'] = $model->is_changed;
            $order['updated_by_admin'] = $model->updated_by_admin;
        }else{
            $order['changes_approved'] = $model->changes_approved;
        }

        $warehouses = Warehouse::select(['id','name','sale_tax'])->get()->toArray();
        $stores = Store::select(['id','name'])->get()->toArray();

        // echo '<pre>';
        // print_r($order);
        // exit; 

        return Inertia::render('ShopForMe/EditOrder',[
            'order' => $order,
            'warehouses' => $warehouses,
            'stores' => $stores,
            'salePrice' => (int)$price_with_tax - $price,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateOrder(Request $request) {
        // dd($request->all());
        $id = $request->input('id');
        $order = Order::findOrFail($id);
        $rules =[
            'form_type' => 'required|in:shopping,pickup',
            'warehouse_id' => 'required',
            'notes' => 'nullable|string',
            'items' => 'required',
            'site_name' => 'required_if:form_type,shopping',
            'shop_url' => 'required_if:form_type,shopping',
            'store_id' => 'required_if:form_type,pickup',
            // 'pickup_type' => 'required_if:form_type,pickup',
            'pickup_date' => 'required_if:form_type,pickup',
            'receipt_url' => 'required_if:is_complete_shopping,1'
        ];

        $validator = Validator::make($request->all(), $rules , $message = [
            'receipt_url.required_if' => 'image of an order is missing',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        $isAdmin = (Auth::user()->type == 'admin') ? true : false ;
        try {
            DB::beginTransaction();

            $order->warehouse_id = $request->warehouse_id;
            $order->notes = $request->notes;

            if(!$isAdmin && $request->has('changes_approved')){
                $order->changes_approved = $request->changes_approved;
                $order->updated_by_admin = false;
                $order->is_changed = false;
            }
            
            if(isset($request->shipping_from_shop)){
                $order->shipping_from_shop = $request->shipping_from_shop;
            }
            if(isset($request->sales_tax)){
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
    
    
                if(!is_object($package)){
    
                    $count = Package::where([
                        'customer_id' => $order->customer_id,
                    ])->count();
    
                    $next = (int) $count +1;
                    $package = new Package();
    
                    $package->package_no = 'Pkg-'.$order->customer_id.'-'.$next;
                    $package->customer_id = $order->customer_id;
                    $package->warehouse_id =  $request->input('warehouse_id');
                    $package->status = 'open';
                    $package->package_handler_id = ($isAdmin) ? Auth::user()->id : null;
                    $package->save();
    
                }

                if($order->order_origin == 'shopping'){
                    $order->received_from = $order->site_name;
                }else if($order->order_origin == 'pickup'){
                    $order->received_from = $order->store->name;
                }

                $files = $request->file();

                if(isset($files['receipt_url'])){
                    $image_object = $files['receipt_url'];
                    $file_name = time().'_'.$image_object->getClientOriginalName();
                    $image_object->storeAs('uploads', $file_name);
                    if($_SERVER['HTTP_HOST'] == 'localhost:8000'){
                        File::move(storage_path('app/uploads/'.$file_name), public_path('/uploads/'.$file_name) );
                    }else{
                        File::move(storage_path('app/uploads/'.$file_name), public_path('../uploads/'.$file_name) );
                    }
                    $order->receipt_url = $file_name;
                }

                $order->package_id = $package->id;
                $order->order_type = 'order';
                $order->status = 'arrived';
                $order->save();

                event(new ShoppingCompletedEvent($order));

            }

            $order->save();

            $checkChanges = $order->wasChanged();

            $orderItemChanges[] = [];

            $items = $request->items;

            foreach($items as $item){

                $item_id = isset($item['id']) ? (int) $item['id'] : 0;

                $order_item = OrderItem::find($item_id);
                //update if existing, else creat new.

                if(!is_object($order_item)){
                    $order_item = new OrderItem();
                }

                $order_item->order_id = $order->id;
                $order_item->name = $item['name'];
                $order_item->description = $item['description'];
                $order_item->quantity = $item['qty'];
                $order_item->url = $item['url'];
                $order_item->unit_price = $item['price'];
                $order_item->price_with_tax = $item['price_with_tax'];
                $order_item->sub_total = $item['sub_total'];

                $order_item->save();

                $orderItemChanges[] = $order_item->wasChanged();

            }



            if($request->is_complete_shopping != 1 && $isAdmin){

                $order->updated_by_admin = true;
                $order->changes_approved = false;

                if($checkChanges != null || $orderItemChanges != null){
                    $order->is_changed = true;
                    $order->save();
                }
                event(new OrderChangesByAdminEvent($order));
            }

            if(!$isAdmin && $request->has('changes_approved')){
                event(new OrderChangesAcceptedByCustomerEvent($order));
            }

            if ($request->form_type == 'pickup') {

                $files = $request->file();

                if(isset($files['image'])){
                    
                    $image_object = $files['image'];

                    $file_name = time().'_'.$image_object->getClientOriginalName();
                    $image_object->storeAs('uploads', $file_name);

                    if($_SERVER['HTTP_HOST'] == 'localhost:8000'){
                        File::move(storage_path('app/uploads/'.$file_name), public_path('/uploads/'.$file_name) );
                    }else{
                        File::move(storage_path('app/uploads/'.$file_name), public_path('../uploads/'.$file_name) );
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
            if(!$isAdmin && $request->has('changes_approved')){
                return redirect()->route('payment.index','amount='.$order->grand_total);
            }else{
                return redirect('shop-for-me')->with('success', 'Order Updated !');
            }

        } catch (\Exception $e) {
            return redirect('shop-for-me')->with('error', 'Something went wrong: '. $e->getMessage());
            DB::rollBack();
        }
    }


    public function filterStores($id) {
        $stores = Store::where('warehouse_id', $id)->get();        
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

        $coupon = Coupon::where('code',$code)->first();

        if($coupon != null){
            $strCheck = strcmp($code,$coupon->code);
            if($strCheck == 0){
                $checkCode = CustomerCoupon::where('coupon_id',$coupon->id)->where('customer_id',$customer->id)->get();
                if($checkCode->count() > 0){
                    return response()->json([
                        'status' => 0,
                        'message' => 'Coupon already used',
                    ]);
                }else{
                    return response()->json([
                        'status' => 1,
                        'message' => 'Coupon Applied',
                        'discount' => $coupon->discount,
                    ]);
                }

            }else{
                return response()->json([
                    'status' => 0,
                    'message' => 'Coupon is invalid',
                ]);
            }
        }else{
            return response()->json([
                'status' => 0,
                'message' => 'Coupon is invalid',
            ]);
        }

    }
}
