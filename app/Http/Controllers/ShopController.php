<?php

namespace App\Http\Controllers;

use App\Events\ShoppingCompletedEvent;
use App\Events\ShoppingCreatedEvent;
use App\Models\Order;
use App\Models\OrderImage;
use App\Models\OrderItem;
use App\Models\Store;
use App\Models\Warehouse;
use App\Models\Package;
use App\Notifications\ShoppingCreated;
use Illuminate\Http\Request;
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
        // dd($request->all());
        $request->validate([
            'form_type' => 'required|in:shopping,pickup',
            'warehouse_id' => 'required',
            'notes' => 'required|string',
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

            foreach($request->items as $item){

                $order_item = new OrderItem();

                $order_item->order_id = $order->id;
                $order_item->name = $item['name'];
                $order_item->description = $item['option'];
                $order_item->quantity = $item['qty'];
                $order_item->unit_price = $item['price'];
                $order_item->price_after_tax = $item['price_after_tax'];
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

            event(new ShoppingCreatedEvent($order));

            return redirect('shop-for-me')->with('success', 'Order Added!');

        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
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
        $price_after_tax = $model->items->sum('price_after_tax');

        foreach($model->items as $item){
            $items[] = [
                'id' => $item->id,
                'name' => $item->name,
                'option' => $item->description,
                'qty' => $item->quantity,
                'price' => $item->unit_price,
                'price_after_tax' => $item->price_after_tax,
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
            'image' => '',
            'items' => $items,
            'images' => $images,
        ];

        $warehouses = Warehouse::select(['id','name'])->get()->toArray();
        $stores = Store::select(['id','name'])->get()->toArray();

        // echo '<pre>';
        // print_r($order);
        // exit; 

        return Inertia::render('ShopForMe/EditOrder',[
            'order' => $order,
            'warehouses' => $warehouses,
            'stores' => $stores,
            'salePrice' => (int)$price_after_tax - $price,
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
        $request->validate([
            'form_type' => 'required|in:shopping,pickup',
            'warehouse_id' => 'required',
            'notes' => 'required||string',
            'items' => 'required',
            'site_name' => 'required_if:form_type,shopping',
            'shop_url' => 'required_if:form_type,shopping',
            'store_id' => 'required_if:form_type,pickup',
            // 'pickup_type' => 'required_if:form_type,pickup',
            'pickup_date' => 'required_if:form_type,pickup'
        ]);

        try {
            DB::beginTransaction();

            $order->warehouse_id = $request->warehouse_id;
            $order->notes = $request->notes;
            
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
                    $package->save();
    
                }

                if($order->order_origin == 'shopping'){
                    $order->received_from = $order->site_name;
                }else if($order->order_origin == 'pickup'){
                    $order->received_from = $order->store->name;
                }

                $order->package_id = $package->id;
                $order->order_type = 'order';
                $order->status = 'arrived';
                $order->save();

                event(new ShoppingCompletedEvent($order));

            }

            $order->save();

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
                $order_item->description = $item['option'];
                $order_item->quantity = $item['qty'];
                $order_item->url = $item['url'];
                $order_item->unit_price = $item['price'];
                $order_item->price_after_tax = $item['price_after_tax'];

                $order_item->save();

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
            return redirect('shop-for-me')->with('success', 'Order Updated !');
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
}
