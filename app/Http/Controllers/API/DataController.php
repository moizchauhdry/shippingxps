<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Address;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DataController extends BaseController
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

    public function addresses()
    {
        $user = Auth::user();
        $data['addresses'] = Address::where('user_id', $user->id)->orderBy('id', 'desc')->get();

        return $this->sendResponse($data, 'success');
    }
}
