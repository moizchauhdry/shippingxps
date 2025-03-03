<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Address;
use App\Models\Country;
use App\Models\Package;
use App\Models\SignatureType;
use App\Models\SiteSetting;
use GuzzleHttp\Client;
use Illuminate\Validation\Rule;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = Address::orderBy('id', 'desc')->with('country')->where('user_id', Auth::user()->id)->get();

        return Inertia::render('Address/AddressList', [
            'addresses' => $addresses
        ]);
    }

    public function create()
    {
        $countries = Country::all(['id', 'nicename as name', 'iso'])->toArray();
        // $signature_types = SignatureType::where('active', 1)->get();

        return Inertia::render('Address/CreateAddress', [
            'countries' => $countries,
            // 'signature_types' => $signature_types,
        ]);
    }

    public function store(Request $request)
    {        
        $validated = $request->validate([
            'fullname' => 'regex:/^[A-Za-z0-9\s]+$/|required',
            'is_residential' => 'required|boolean',
            'country_id' => 'required',
            'city' => 'regex:/^[A-Za-z0-9\s]+$/|required|string',
            'zip_code' => 'regex:/^[A-Za-z0-9\s]+$/|required|string',
            'phone' => 'required|string',
            'email' => 'email|required|string',
            'address' => 'regex:/^[A-Za-z0-9\s\-\#\,\/\\\\]+$/|required|string|max:35',
            'address_2' => 'regex:/^[A-Za-z0-9\s\-\#\,\/\\\\]+$/|nullable|string|max:35',
            'address_3' => 'regex:/^[A-Za-z0-9\s\-\#\,\/\\\\]+$/|nullable|string|max:35',
            'tax_no' => 'nullable|max:100',
            // 'signature_type_id' => 'required',
        ], [
            'regex' => 'The :attribute must only contain letters (english) and numbers.'
        ]);


        $country = Country::find($request->country_id);
        $country_code = $country->iso;

        if ($request->state_required == true) {
            $validated += $request->validate([
                'state' => 'required|min:2|max:2',
            ]);

            try {

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

                // if ($address_type == 'UNKNOWN') {
                //     return redirect()->back()->with('error', 'The address you have entered is not valid.');
                // }
                
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', 'The address you have entered is not valid.');
            }
        }

        $user = Auth::user();

        $data = [
            'user_id' => $user->id,
            'fullname' => $request->fullname,
            'country_id' => $request->country_id,
            'country_code' => $country_code,
            'state' => $request->state,
            'city' => $request->city,
            'zip_code' => $request->zip_code,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'address_2' => $request->address_2,
            'address_3' =>  $request->address_3,
            'is_residential' => $request->is_residential,
            'tax_no' => $request->tax_no,
        ];

        $address = Address::where('user_id', $user->id)->where('address', $request->address)->first();
        if ($address) {
            $address->update($data);
        } else {
            $address = Address::create($data);
        }
        
        if ($request->packages_address == true) {

            $package = Package::find($request->package_id);

            if (isset($address)) {
                $address_type = $address->country_id == 226 ? 'domestic' : 'international';
                $package->update([
                    'address_book_id' => $address->id,
                    'address_type' => $address_type,
                ]);

                $address->update(['user_id' => $package->customer_id]); // due to admin add or edit package
            } else {
                $package->update([
                    'address_book_id' => 0,
                    'address_type' => NULL,
                ]);
            }
    
            if ($address->signature_type_id == 5) {
                $signature_charges = SiteSetting::where('name', 'signature_charges')->first();
                $package->update(['signature_charges' => $signature_charges->value]);
            } else {
                $package->update(['signature_charges' => 0]);
            }

            return redirect()->back()->with('success', 'The address have been created successfully.');
        } else {
            return redirect()->route('addresses')->with('success', 'The address have been created successfully.');
        }
    }

    public function edit($id)
    {
        $address = Address::find($id);

        $countries = Country::all(['id', 'nicename as name', 'iso'])->toArray();
        // $signature_types = SignatureType::where('active', 1)->get();

        return Inertia::render('Address/EditAddress', [
            'address' => $address,
            'countries' => $countries
            // 'signature_types' => $signature_types,
        ]);
    }

    public function update(Request $request)
    {
        $id = $request->input('id');
        $address = Address::find($id);

        $validated = $request->validate([
            'fullname' => 'regex:/^[A-Za-z0-9\s]+$/|required',
            'is_residential' => 'required|boolean',
            'country_id' => 'required',
            'city' => 'regex:/^[A-Za-z0-9\s]+$/|required|string',
            'zip_code' => 'regex:/^[A-Za-z0-9\s]+$/|required|string',
            'phone' => 'required|string',
            'email' => 'email|required|string',
            'address' => 'regex:/^[A-Za-z0-9\s\-\#\,\/\\\\]+$/|required|string|max:35',
            'address_2' => 'regex:/^[A-Za-z0-9\s\-\#\,\/\\\\]+$/|nullable|string|max:35',
            'address_3' => 'regex:/^[A-Za-z0-9\s\-\#\,\/\\\\]+$/|nullable|string|max:35',
            'tax_no' => 'nullable|max:100',
            // 'signature_type_id' => 'required',
        ], [
            'regex' => 'The :attribute must only contain letters (english) and numbers.'
        ]);


        $country = Country::find($request->country_id);
        $country_code = $country->iso;

        if ($request->state_required == true) {
            $validated += $request->validate([
                'state' => 'required|min:2|max:2',
            ]);

            try {
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
        }

        $address->user_id = Auth::user()->id;
        $address->fullname = $validated['fullname'];
        $address->country_id = $validated['country_id'];
        $address->country_code = $country_code;
        $address->state = $request->state;
        $address->city = $validated['city'];
        $address->zip_code = $validated['zip_code'];
        $address->phone = $validated['phone'];
        $address->email = $validated['email'];
        $address->address = $validated['address'];
        $address->address_2 = $request->address_2;
        $address->address_3 =  $request->address_3;
        $address->is_residential = $validated['is_residential'];
        $address->tax_no = $request->tax_no;
        // $address->signature_type_id = $request->signature_type_id;
        $address->update();

        if ($request->packages_address == true) {
            return redirect()->back()->with('success', 'The address have been created successfully.');
        } else {
            return redirect()->route('addresses')->with('success', 'The address have been created successfully.');
        }
    }

    public function destroy($id)
    {
        Address::find($id)->delete();
        return back()->with('success', 'Address deleted!');
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

    public function getAddressByID(Request $request)
    {
        $address = Address::where('id',$request->address_book_id)->first();
        // dd($address);
        
        if ($address) {
            return response()->json($address);
        }

        return response()->json(['error' => 'Address not found'], 404);
    }
}
