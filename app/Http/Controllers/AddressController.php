<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use \Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Address;
use App\Models\Country;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $addresses = Address::with('country')->where('user_id', Auth::user()->id)->get();

        return Inertia::render('Address/AddressList', [
            'addresses' => $addresses
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countries = Country::all(['id', 'nicename as name', 'iso'])->toArray();

        return Inertia::render('Address/CreateAddress', [
            'countries' => $countries,
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
        $validated = $request->validate([
            'fullname' => 'required|string',
            'country_id' => 'required',
            'state' => 'nullable|string',
            'city' => 'required|string',
            'zip_code' => 'required|string',
            'phone' => 'required|string',
            'address' => 'required|string',
            'is_residential' => 'required|boolean',
        ]);

        $address = new Address();
        $address->user_id = Auth::user()->id;
        $address->fullname = $validated['fullname'];
        $address->country_id = $validated['country_id'];
        $address->state = $validated['state'];
        $address->city = $validated['city'];
        $address->zip_code = $validated['zip_code'];
        $address->phone = $validated['phone'];
        $address->address = $validated['address'];
        $address->is_residential = $validated['is_residential'];

        $address->save();

        return redirect('addresses')->with('success', 'Address Created Successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $model = Address::find($id);

        $address = [
            'id' => $model->id,
            'fullname' => $model->fullname,
            'country_id' => $model->country_id,
            'state' => $model->state,
            'city' => $model->city,
            'zip_code' => $model->zip_code,
            'phone' => $model->phone,
            'address' => $model->address,
            'is_residential' => $model->is_residential,
        ];

        $countries = Country::all(['id', 'nicename as name', 'iso'])->toArray();

        return Inertia::render('Address/EditAddress', [
            'address' => $address,
            'countries' => $countries
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

        $id = $request->input('id');

        $address = Address::find($id);

        $validated = $request->validate([
            'fullname' => 'required|string',
            'country_id' => 'required',
            'state' => 'required|string',
            'city' => 'required|string',
            'zip_code' => 'required|string',
            'phone' => 'required|string',
            'address' => 'required|string',
            'is_residential' => 'required|boolean',
        ]);


        $address->user_id = Auth::user()->id;
        $address->fullname = $validated['fullname'];
        $address->country_id = $validated['country_id'];
        $address->state = $validated['state'];
        $address->city = $validated['city'];
        $address->zip_code = $validated['zip_code'];
        $address->phone = $validated['phone'];
        $address->address = $validated['address'];
        $address->is_residential = $validated['is_residential'];

        $address->update();

        return redirect()->back()->with('success', 'Address Updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        Address::find($id)->delete();

        return back()->with('success', 'Address deleted!');

        //return response()->json(['status' => TRUE]);

    }

    public function suite()
    {


        $suite_no = Auth::user()->suite_no;

        $name = Auth::user()->name;
        $response['warehouses'] = Warehouse::all();

        $addresses = [
            0 => [
                'name' => 'New Jersey Address',
                'address' =>  $name . ' <br> 4 TAFT RD,<br> SUITE #' . $suite_no . '<br> TOTOWA <br> NEW JERSEY 07512 <br> 302 265 0777'
            ],
            1 => [
                'name' => 'Tax Free Delaware Address',
                'address' =>  $name . '<br> 1217 OLD COOCHS BRIDGE RD <br> SUITE #' . $suite_no . '<br> NEWARK <br> DELAWARE 19713 <br>301 265 0777',
            ]
        ];

        $addresses = $response['warehouses'];

        /*
        $addresses = [
            'New Jersey Address' =>  [
                'fullname' => $name,
                'address1' => '4 TAFT RD',
                'address2' => 'SUITE #'.$suite_no,
                'city' => 'TOTOWA',
                'state' => 'NEW JERSEY',
                'zip' => '07512',
                'phone' => '302 265 0777'
            ],
            'Tax Free Delaware Address' =>  [
                'fullname' => $name,
                'address1' => '1217 OLD COOCHS BRIDGE RD',
                'address2' => 'SUITE #'.$suite_no,
                'city' => 'NEWARK',
                'state' => 'DELAWARE',
                'zip' => '19713',
                'phone' => '302 265 0777'
            ]
        ];
        */

        return Inertia::render('Address/SuiteAddress', [
            'addresses' => $addresses
        ]);
    }

    public function getShippingAddress($id)
    {
        $address = Address::find($id);

        if ($address != NULL) {
            return response()->json(['status' => true, 'address' => $address]);
        } else {
            return response()->json(['status' => false, 'address' => NULL]);
        }
    }
}
