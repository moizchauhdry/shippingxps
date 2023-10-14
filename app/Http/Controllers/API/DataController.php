<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class DataController extends Controller
{
    public function index()
    {
        $countries = Country::orderBy('name', 'asc')->get();

        $data = [
            'countries' => $countries
        ];
        
        return response()->json([
            'status' => true,
            'message' => 'success',
            'data' => $data,
        ]);
    }
}
