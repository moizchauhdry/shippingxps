<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\AuctionImage;
use App\Models\AuctionCategory;
use App\Models\Package;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use File;

class AuctionController extends Controller
{
    public function index()
    {
        $packages = Package::paginate(10);
        return Inertia::render('Auctions/Index',[
            'packages' => $packages
        ]);   
    }

    public function listing(Request $request)
    {
        

        $query = Auction::when($request->status && !empty($request->status), function ($qry) use ($request) {
                if($request->status == 1){
                    $qry->where('status', 1);
                }else{
                    $qry->where('status', 0);
                }
            })
            ->when($request->auction_category_id && !empty($request->auction_category_id), function ($qry) use ($request) {
                $qry->where('auction_category_id', $request->auction_category_id);
            })
            ->when($request->date_range && !empty($request->date_range), function ($qry) use ($request) {
                $range = explode(' - ', $request->date_range);
                $from = date("Y-m-d", strtotime($range[0]));
                $to = date("Y-m-d", strtotime($range[1]));
                $qry->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to);
            });

        $auctions = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        $categories = AuctionCategory::all();
        return Inertia::render('Auctions/Listing',[
            'auctions' => $auctions,
            'categories' => $categories,
            'filters' => [
                'status' => $request->status ?? "",
                'auction_category_id' => $request->auction_category_id ?? "",
                'date_range' => $request->date_range ?? "",
            ]
        ]);
    }

    public function create(Request $request)
    {
        $categories = AuctionCategory::all();
        return Inertia::render('Auctions/CreatePage', [
            'categories' => $categories
        ]);

    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            "name" => 'required',
            "auction_category_id" => 'required',
            "description" => 'required',
            'weight_unit' => 'required',
            'dim_unit' => 'required',
            'package_weight' => 'required|numeric|gt:0',
            'package_length' => 'required|numeric|gt:0',
            'package_width' => 'required|numeric|gt:0',
            'package_height' => 'required|numeric|gt:0',
            "starting_price" => 'required|numeric|gt:0',
            "ending_at" => 'required',
            "images" => 'required|array'
        ]);


        $auction = Auction::create([
            "name" => $request->name,
            "auction_category_id" => $request->auction_category_id,
            "description" => $request->description,
            "weight_unit" => $request->weight_unit,
            "dimension_unit" => $request->dim_unit,
            "weight" => $request->package_weight,
            "length" => $request->package_length,
            "width" => $request->package_width,
            "height" => $request->package_height,
            "starting_price" => $request->starting_price,
            "ending_at" => $request->ending_at,
        ]);

        $files = $request->images;
            if (isset($files)) {
                foreach ($files as $key => $file) {
                    
                    $image_object = $file['image'];
                    $file_name = time() . '_' . $auction->id.'.'.$file['image']->getClientOriginalExtension();
                    $image_object->storeAs('uploads', $file_name);
                    if ($_SERVER['HTTP_HOST'] == 'localhost:8000') {
                        File::move(storage_path('app/uploads/' . $file_name), public_path('/public/uploads/' . $file_name));
                    } else {
                        File::move(storage_path('app/uploads/' . $file_name), public_path('../public/uploads/' . $file_name));
                    }
                    $auction_image = new AuctionImage();
                    $auction_image->image = $file_name;
                    $auction_image->auction_id = $auction->id;
                    if ($key == 0) {
                        $auction_image->featured = 1;
                    } else {
                        $auction_image->featured = 0;
                    }
                    $auction_image->save();
                }
            }

            return redirect()->route('auctions.listing')->with('success', 'The auction has been added successfully.');
        
        
    }

    public function show($id)
    {
        $auction = Auction::with('category')->find($id);
        $auctionImages = AuctionImage::where('auction_id',$id)->get();
        return Inertia::render('Auctions/DetailPage', [
            'auction' => $auction,
            'auction_images' => $auctionImages,
        ]);

    }

    public function edit($id)
    {
        $auction = Auction::find($id);
        $categories = AuctionCategory::all();
        $auctionImages = AuctionImage::where('auction_id',$id)->get();
        return Inertia::render('Auctions/EditPage', [
            'auction' => $auction,
            'auction_images' => $auctionImages,
            'categories' => $categories
        ]);

    }

    public function update(Request $request,$id)
    {


        $auction = Auction::find($id);
        $validated = $request->validate([
            "name" => 'required',
            "auction_category_id" => 'required',
            "description" => 'required',
            'weight_unit' => 'required',
            'dim_unit' => 'required',
            'package_weight' => 'required|numeric|gt:0',
            'package_length' => 'required|numeric|gt:0',
            'package_width' => 'required|numeric|gt:0',
            'package_height' => 'required|numeric|gt:0',
            "starting_price" => 'required|numeric|gt:0',
            "ending_at" => 'required'
        ]);


        $auction->update([
            "name" => $request->name,
            "auction_category_id" => $request->auction_category_id,
            "description" => $request->description,
            "weight_unit" => $request->weight_unit,
            "dimension_unit" => $request->dim_unit,
            "weight" => $request->package_weight,
            "length" => $request->package_length,
            "width" => $request->package_width,
            "height" => $request->package_height,
            "starting_price" => $request->starting_price,
            "ending_at" => $request->ending_at,
        ]);

        $files = $request->images;
            if (isset($files)) {
                foreach ($files as $key => $file) {
                    if($file['image'] != null){
                        $image_object = $file['image'];
                        $file_name = time() . '_' . $auction->id.'.'.$file['image']->getClientOriginalExtension();
                        $image_object->storeAs('uploads', $file_name);
                        if ($_SERVER['HTTP_HOST'] == 'localhost:8000') {
                            File::move(storage_path('app/uploads/' . $file_name), public_path('/public/uploads/' . $file_name));
                        } else {
                            File::move(storage_path('app/uploads/' . $file_name), public_path('../public/uploads/' . $file_name));
                        }
                        $auction_image = new AuctionImage();
                        $auction_image->image = $file_name;
                        $auction_image->auction_id = $auction->id;
                        if ($key == 0) {
                            $auction_image->featured = 1;
                        } else {
                            $auction_image->featured = 0;
                        }
                        $auction_image->save();
                    }                    
                }
            }

            return redirect()->route('auctions.listing')->with('success', 'The auction has been updated successfully.');
        
        
    }

    public function deleteImage(Request $request)
    {
        $image_id = $request->input('id');
        AuctionImage::find($image_id)->delete();

        return response()->json([
            'status' => 1
        ]);
    }
}
