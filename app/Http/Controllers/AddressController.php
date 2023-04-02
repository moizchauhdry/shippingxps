<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Address;
use App\Models\Country;
use GuzzleHttp\Client;

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
            'state' => 'required|string|min:2|max:2',
            'city' => 'required|string',
            'zip_code' => 'required|string',
            'phone' => 'required|string',
            'email' => 'email|required|string',
            'address' => 'required|string|max:35',
            'address_2' => 'nullable|string|max:35',
            'address_3' => 'nullable|string|max:35',
            'is_residential' => 'required|boolean',
        ]);


        try {

            $country = Country::find($request->country_id);
            $country_code = $country->iso;

            $client = new Client();

            $result = $client->post('https://apis.fedex.com/oauth/token', [
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => 'l7ef7275cc94544aaabf802ef4308bb66a',
                    'client_secret' => '48b51793-fd0d-426d-8bf0-3ecc62d9c876',
                ]
            ]);

            $authorization = $result->getBody()->getContents();
            $authorization = json_decode($authorization);

            $headers = [
                'X-locale' => 'en_US',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $authorization->access_token
            ];

            $body = [
                "inEffectAsOfTimestamp" => "2019-09-06",
                "validateAddressControlParameters" => [
                    "includeResolutionTokens" => true
                ],
                "addressesToValidate" => [
                    [
                        "address" => [
                            "streetLines" => [
                                $validated['address']
                            ],
                            "city" =>  $validated['city'],
                            "stateOrProvinceCode" =>  $validated['state'],
                            "postalCode" =>  $validated['zip_code'],
                            "countryCode" => $country_code,
                            "urbanizationCode" => "string",
                            "addressVerificationId" => "string"
                        ]
                    ]
                ]
            ];

            $api_request = $client->post('https://apis.fedex.com/address/v1/addresses/resolve', [
                'headers' => $headers,
                'body' => json_encode($body)
            ]);

            $response = $api_request->getBody()->getContents();
            $response = json_decode($response);

            $address_type = $response->output->resolvedAddresses[0]->classification;

            if ($address_type == 'BUSINESS' && $request->is_residential == 1) {
                return redirect()->back()->with('error', 'The address you have entered is business but you select residential.');
            }

            if ($address_type == 'RESIDENTIAL' && $request->is_residential == 0) {
                return redirect()->back()->with('error', 'The address you entered is residential but you select business.');
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'The address you have entered is not valid.');
        }

        $address = new Address();
        $address->user_id = Auth::user()->id;
        $address->fullname = $validated['fullname'];
        $address->country_id = $validated['country_id'];
        $address->country_code = $country_code;
        $address->state = $validated['state'];
        $address->city = $validated['city'];
        $address->zip_code = $validated['zip_code'];
        $address->phone = $validated['phone'];
        $address->email = $validated['email'];
        $address->address = $validated['address'];
        $address->address_2 = $request->address_2;
        $address->address_3 =  $request->address_3;
        $address->is_residential = $validated['is_residential'];
        $address->save();

        return redirect()->back()->with('success', 'The address have been created successfully.');
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
            'state' => 'required|string|min:2|max:2',
            'city' => 'required|string',
            'zip_code' => 'required|string',
            'phone' => 'required|string',
            'email' => 'email|required|string',
            'address' => 'required|string|max:35',
            'address_2' => 'nullable|string|max:35',
            'address_3' => 'nullable|string|max:35',
            'is_residential' => 'required|boolean',
        ]);


        try {

            $country = Country::find($request->country_id);
            $country_code = $country->iso;

            $client = new Client();

            $result = $client->post('https://apis.fedex.com/oauth/token', [
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => 'l7ef7275cc94544aaabf802ef4308bb66a',
                    'client_secret' => '48b51793-fd0d-426d-8bf0-3ecc62d9c876',
                ]
            ]);

            $authorization = $result->getBody()->getContents();
            $authorization = json_decode($authorization);

            $headers = [
                'X-locale' => 'en_US',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $authorization->access_token
            ];

            $body = [
                "inEffectAsOfTimestamp" => "2019-09-06",
                "validateAddressControlParameters" => [
                    "includeResolutionTokens" => true
                ],
                "addressesToValidate" => [
                    [
                        "address" => [
                            "streetLines" => [
                                $validated['address']
                            ],
                            "city" =>  $validated['city'],
                            "stateOrProvinceCode" =>  $validated['state'],
                            "postalCode" =>  $validated['zip_code'],
                            "countryCode" => $country_code,
                            "urbanizationCode" => "string",
                            "addressVerificationId" => "string"
                        ]
                    ]
                ]
            ];

            $api_request = $client->post('https://apis.fedex.com/address/v1/addresses/resolve', [
                'headers' => $headers,
                'body' => json_encode($body)
            ]);

            $response = $api_request->getBody()->getContents();
            $response = json_decode($response);

            $address_type = $response->output->resolvedAddresses[0]->classification;

            if ($address_type == 'BUSINESS' && $request->is_residential == 1) {
                return redirect()->back()->with('error', 'The address you have entered is business but you select residential.');
            }

            if ($address_type == 'RESIDENTIAL' && $request->is_residential == 0) {
                return redirect()->back()->with('error', 'The address you entered is residential but you select business.');
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'The address you have entered is not valid.');
        }

        $address->user_id = Auth::user()->id;
        $address->fullname = $validated['fullname'];
        $address->country_id = $validated['country_id'];
        $address->country_code = $country_code;
        $address->state = $validated['state'];
        $address->city = $validated['city'];
        $address->zip_code = $validated['zip_code'];
        $address->phone = $validated['phone'];
        $address->email = $validated['email'];
        $address->address = $validated['address'];
        $address->address_2 = $request->address_2;
        $address->address_3 =  $request->address_3;
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
