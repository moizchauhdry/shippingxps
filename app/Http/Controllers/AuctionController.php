<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AuctionController extends Controller
{
    public function index()
    {
        $packages = Package::paginate(10);
        return Inertia::render('Auctions/Index',[
            'packages' => $packages
        ]);   
    }
}
