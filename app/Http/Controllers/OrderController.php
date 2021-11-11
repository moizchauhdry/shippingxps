<?php

namespace App\Http\Controllers;

use App\Events\OrderCreatedEvent;
use App\Events\OrderUpdatedEvent;
use Illuminate\Http\Request;
use \Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use GuzzleHttp\Client;
use App\Models\Order;
use App\Models\City;
use App\Models\OrderItem;
use App\Models\OrderImage;
use App\Models\Package;
use App\Models\User;
use App\Models\SiteSetting;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Auth;
use File;


class OrderController extends Controller
{
    public $status_list = ['arrived', 'labeled', 'shipped', 'delivered', 'rejected'];

    public function index(Request $request)
    {

        // $orders = Order::with(['warehouse']);
        // $orders= new Order();
        $search = '';
        $customer_id = '';

        $customers = User::where('type', '=', 'customer')->select(['id', 'name'])->get()->toArray();

        if ((isset($_GET['search']) && !empty($_GET['search']))) {


            $search = $_GET['search'];
            $search = trim($search);

            $orders = Order::whereHas('user', function ($query) use ($search) {
                $query->where('name', $search);
            })->with(['user' => function ($query) use ($search) {
                $query->where('name', $search);
            }, 'warehouse']);
        } else {
            $orders = Order::with(['customer', 'warehouse']);
        }

        $status = isset($_GET['status']) ? $_GET['status'] : '';

        if (!empty($status)) {
            $status = in_array($status, ['arrived', 'labeled', 'shipped', 'delivered', 'rejected']) ? $status : '';
        }


        if (!empty($status)) {
            $orders->where('status', '=', $status);
        }

        $user = Auth::user();
        if ($user->type == 'customer') {
            $orders->where('customer_id', $user->id);
        } else if (!empty($customer_id)) {
            $orders->where('customer_id', $customer_id);
        }

        $orders->where('order_type', 'order');
        $orders->orderBy('id', 'DESC');
//        $query = $orders;
//        $query1 = $orders;
//        $query2 = $orders;
//        $orders = $orders->paginate(25);
        $arrived = $orders->where('status', 'like' ,'arrived')->get();
        $labeled = $orders->where('status', 'like' ,'labeled')->get();
        $shipped = $orders->where('status', 'like' ,'shipped')->get();

        return Inertia::render('Orders/OrdersList', [
            'search' => $search,
            'orders' => $orders,
            'customers' => $customers,
            'customer_id' => $customer_id,
            'arrived' => $arrived,
            'labeled' => $labeled,
            'shipped' => $shipped
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $order = Order::with(['customer', 'items', 'warehouse', 'images'])->findOrFail($id);

        return Inertia::render('Orders/OrderDetail', ['order' => $order]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        $selected_customer = $request->get('customer_id', 0);

        $customers = User::where('type', '=', 'customer')->select(['id', 'name'])->get()->toArray();

        $warehouses = Warehouse::select(['id', 'name', 'sale_tax'])->get()->toArray();

        return Inertia::render('Orders/CreateOrder', [
            'customers' => $customers,
            'selected_customer' => $selected_customer,
            'warehouses' => $warehouses,
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validated = $request->validate([
            'customer_id' => 'required',
            'tracking_number_in' => 'required|string',
            'warehouse_id' => 'required',
            'weight_unit' => 'required',
            'dim_unit' => 'required',
            'package_weight' => 'required|numeric|gt:0',
            'package_length' => 'required|numeric|gt:0',
            'package_width' => 'required|numeric|gt:0',
            'package_height' => 'required|numeric|gt:0',
            'notes' => 'string',
            'received_from' => 'required|string',
        ]);

        try {
            DB::beginTransaction();


            $package = Package::where([
                'status' => 'open',
                'customer_id' => $validated['customer_id'],
                'warehouse_id' => $validated['warehouse_id']
            ])->get()->first();


            if (!is_object($package)) {

                $count = Package::where([
                    'customer_id' => $validated['customer_id']
                ])->count();

                $next = (int)$count + 1;
                $package = new Package();

                $package->package_no = 'Pkg-' . $validated['customer_id'] . '-' . $next;
                $package->customer_id = $validated['customer_id'];
                $package->warehouse_id = $validated['warehouse_id'];
                $package->status = 'open';
                $package->save();

            }

            $order = new Order();
            $order->package_id = $package->id;
            $order->customer_id = $validated['customer_id'];
            $order->warehouse_id = $validated['warehouse_id'];
            $order->tracking_number_in = $validated['tracking_number_in'];

            //package
            $order->weight_unit = $validated['weight_unit'];
            $order->dim_unit = $validated['dim_unit'];
            $order->package_weight = $validated['package_weight'];
            $order->package_length = $validated['package_length'];
            $order->package_width = $validated['package_width'];
            $order->package_height = $validated['package_height'];

            $order->received_from = $validated['received_from'];

            $order->notes = $validated['notes'];

            $order->save();

            $files = $request->file();
            if (isset($files['images'])) {
                foreach ($files['images'] as $key => $file) {

                    $image_object = $file['image'];

                    $file_name = time() . '_' . $image_object->getClientOriginalName();
                    $image_object->storeAs('uploads', $file_name);

                    if ($_SERVER['HTTP_HOST'] == 'localhost:8000') {
                        File::move(storage_path('app/uploads/' . $file_name), public_path('/public/uploads/' . $file_name));
                    } else {
                        File::move(storage_path('app/uploads/' . $file_name), public_path('../public/uploads/' . $file_name));
                    }

                    $order_image = new OrderImage();

                    //$order_image->image = 'default-image.png';
                    $order_image->image = $file_name;
                    $order_image->order_id = $order->id;
                    if ($key == 0) {
                        $order_image->display = 1;
                    } else {
                        $order_image->display = 0;
                    }

                    $order_image->save();
                }
            }

            $items = $request->input('items');

            foreach ($items as $key => $item) {

                $order_item = new OrderItem();
                $order_item->order_id = $order->id;
                $order_item->name = $item['name'];
                $order_item->description = $item['description'];
                $order_item->quantity = $item['qty'];
                /*$order_item->unit_price = $item['price'];
                $order_item->price_with_tax = $item['price_with_tax'];
                $order_item->sub_total = $item['sub_total'];
                $order_item->url = $item['url'];*/

                $file_name = '';

                if (isset($files['items'][$key]['image'])) {

                    $image_object = $files['items'][$key]['image'];
                    $file_name = time() . '_' . $image_object->getClientOriginalName();
                    $image_object->storeAs('uploads', $file_name);

                    if ($_SERVER['HTTP_HOST'] == 'localhost:8000') {
                        File::move(storage_path('app/uploads/' . $file_name), public_path('/public/uploads/' . $file_name));
                    } else {
                        File::move(storage_path('app/uploads/' . $file_name), public_path('../public/uploads/' . $file_name));
                    }
                }

                $order_item->image = $file_name;

                $order_item->save();
            }

            DB::commit();

            event(new OrderCreatedEvent($order));

            return redirect('orders')->with('success', 'Order Added!');

        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            return redirect('orders')->with('error', 'Something went wrong');
        }

    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $model = order::find($id);

        $items = [];

        foreach ($model->items as $item) {
            $items[] = [
                'id' => $item->id,
                'name' => $item->name,
                'description' => $item->description,
                'quantity' => $item->quantity,
                'price' => $item->unit_price,
                'price_with_tax' => $item->price_with_tax,
                'sub_total' => $item->sub_total,
                'url' => $item->url
            ];
        }

        $images = [];

        foreach ($model->images as $image) {
            $images[] = [
                'id' => $image->id,
                'image' => $image->image,
            ];
        }

        $order = [
            'id' => $model->id,
            'customer_id' => $model->customer_id,
            'tracking_number_in' => $model->tracking_number_in,
            'shipping_total' => $model->shipping_total,
            'package_weight' => $model->package_weight,
            'package_length' => $model->package_length,
            'package_width' => $model->package_width,
            'package_height' => $model->package_height,
            'declared_value' => $model->declared_value,
            'warehouse_id' => $model->warehouse_id,
            'weight_unit' => $model->weight_unit,
            'dim_unit' => $model->dim_unit,
            'notes' => $model->notes,
            'status' => $model->status,
            'received_from' => $model->received_from,
            'items' => $items,
            'images' => $images,
        ];

        $warehouses = Warehouse::select(['id', 'name'])->get()->toArray();


        $customers = User::where('type', '!=', 'admin')->select(['id', 'name'])->get()->toArray();

        return Inertia::render('Orders/EditOrder', [
            'order' => $order,
            'customers' => $customers,
            'status_list' => $this->status_list,
            'warehouses' => $warehouses
        ]);

    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

        $id = $request->input('id');
        $statusChanges = false;
        $order = Order::find($id);

        $validated = $request->validate([
            'customer_id' => 'required',
            'tracking_number_in' => 'required|string',
            'warehouse_id' => 'required',
            'weight_unit' => 'required',
            'dim_unit' => 'required',
            'package_weight' => 'required|numeric|gt:0',
            'package_length' => 'required|numeric|gt:0',
            'package_width' => 'required|numeric|gt:0',
            'package_height' => 'required|numeric|gt:0',
            'status' => 'required|string',
            'received_from' => 'required|string',
            'notes' => 'string',
        ]);

        try {
            DB::beginTransaction();

            if ($order->status != $validated['status']) {
                $statusChanges = true;
            }

            $order->status = $validated['status'];
            $order->tracking_number_in = $validated['tracking_number_in'];
            $order->warehouse_id = $validated['warehouse_id'];
            //packagen
            $order->weight_unit = $validated['weight_unit'];
            $order->dim_unit = $validated['dim_unit'];
            $order->package_weight = $validated['package_weight'];
            $order->package_length = $validated['package_length'];
            $order->package_width = $validated['package_width'];
            $order->package_height = $validated['package_height'];
            $order->received_from = $validated['received_from'];

            $order->notes = $validated['notes'];


            $order->update();

            $items = $request->input('items');

            $files = $request->file();
            if (isset($files['images'])) {


                foreach ($files['images'] as $key => $file) {

                    $image_object = $file['image'];

                    $file_name = time() . '_' . $image_object->getClientOriginalName();
                    $image_object->storeAs('uploads', $file_name);

                    if ($_SERVER['HTTP_HOST'] == 'localhost:8000') {
                        File::move(storage_path('app/uploads/' . $file_name), public_path('/public/uploads/' . $file_name));
                    } else {
                        File::move(storage_path('app/uploads/' . $file_name), public_path('../public/uploads/' . $file_name));
                    }

                    $order_image = new OrderImage();

                    //$order_image->image = 'default-image.png';
                    $order_image->image = $file_name;
                    $order_image->order_id = $order->id;
                    if ($key == 0) {
                        $order_image->display = 1;
                    } else {
                        $order_image->display = 0;
                    }

                    $order_image->save();
                }
            }

            foreach ($items as $key => $item) {

                $item_id = isset($item['id']) ? (int)$item['id'] : 0;

                $order_item = OrderItem::find($item_id);
                //update if existing, else creat new.

                if (!is_object($order_item)) {
                    $order_item = new OrderItem();
                }

                $order_item->order_id = $order->id;
                $order_item->name = $item['name'];
                $order_item->description = $item['description'];
                $order_item->quantity = $item['quantity'];
                $order_item->unit_price = $item['price'];
                $order_item->price_with_tax = $item['price_with_tax'];
                $order_item->sub_total = $item['sub_total'];
                $order_item->url = $item['url'];

                /*
                $file_name = '';

                if(isset($files['items'][$key]['image'])) {

                    $image_object = $files['items'][$key]['image'];
                    $file_name = time().'_'.$image_object->getClientOriginalName();
                    $file_path = $image_object->storeAs('uploads', $file_name);
                    //File::move(storage_path('app/uploads/'.$file_name), public_path('../uploads/'.$file_name) );

                    if($_SERVER['HTTP_HOST'] == 'localhost:8000'){
                        File::move(storage_path('app/uploads/'.$file_name), public_path('/uploads/'.$file_name) );
                    }else{
                        File::move(storage_path('app/uploads/'.$file_name), public_path('../uploads/'.$file_name) );
                    }
                }

                if(!empty($file_name)){
                    $order_item->image = $file_name;
                }
                */

                $order_item->save();

            }

            DB::commit();

            event(new OrderUpdatedEvent($order));

            return redirect('orders')->with('success', 'Order Updated !');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('orders')->with('error', $e->getMessage());
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $order = Order::find($id);

        $order->images()->delete();
        $order->items()->delete();

        $order->delete();

        return back()->with('success', 'Order deleted!');

    }

    public function removeItem(Request $request)
    {

        $item_id = $request->input('item_id');
        OrderItem::find($item_id)->delete();
    }

    public function deleteImage(Request $request)
    {
        $image_id = $request->input('id');
        OrderImage::find($image_id)->delete();
    }

}
