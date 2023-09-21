<?php

namespace App\Http\Controllers;

use App\Models\ServicePage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use \Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use GuzzleHttp\Client;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Models\City;
use App\Models\Order;
use App\Models\Country;
use App\Models\Package;
use App\Models\Warehouse;
use App\Models\SiteSetting;
use App\Models\User;
use App\Notifications\AnnouncementNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{

    public $token = 'm99PyQIqoq5GmAKjs1wOTAbhQ0ozkc0s';
    public $cookie = 'PHPSESSID=3rf796ooctiic30gq0e54bpg45';
    public $customer_id = '12339140';
    public $integration_id = '59690';



    public $service_list = [

        //use only fedex and dhl.
        //use logo as well.
        [
            "service_id" => 0,
            "carrierCode" => "dhl",
            "carrierLabel" => "dhl",
            "serviceLabel" => "DHL Intl Express",
            "serviceCode" => "dhl_express_worldwide",
            'packageTypeCode' => 'dhl_custom_package',
            "currency" => "USD",
            "totalAmount" => 0,
            "baseAmount" => 0,
            "isReady" => false,
            "logo" => "/partner-imgs/dhl-logo.png"
        ],
        [
            "service_id" => 1,
            "carrierCode" => "fedex",
            "carrierLabel" => "fedex",
            "serviceLabel" => "FedEx International Economy®",
            "serviceCode" => "fedex_international_economy",
            'packageTypeCode' => 'fedex_custom_package',
            "currency" => "USD",
            "totalAmount" => 0,
            "baseAmount" => 0,
            "isReady" => false,
            "logo" => "/partner-imgs/fedex-logo.png"
        ],
        [
            "service_id" => 2,
            "carrierCode" => "fedex",
            "carrierLabel" => "fedex",
            "serviceLabel" => "FedEx International Ground®",
            "serviceCode" => "fedex_ground_canada",
            'packageTypeCode' => 'fedex_custom_package',
            "currency" => "USD",
            "totalAmount" => 0,
            "baseAmount" => 0,
            "isReady" => false,
            "logo" => "/partner-imgs/fedex-logo.png"
        ],
        [
            "service_id" => 3,
            "carrierCode" => "fedex",
            "carrierLabel" => "fedex",
            "serviceLabel" => "FedEx Standard Overnight®",
            "serviceCode" => "fedex_standard_overnight",
            'packageTypeCode' => 'fedex_custom_package',
            "currency" => "USD",
            "totalAmount" => 0,
            "baseAmount" => 0,
            "isReady" => false,
            "logo" => "/partner-imgs/fedex-logo.png"
        ],
        [
            "service_id" => 4,
            "carrierCode" => "fedex",
            "carrierLabel" => "fedex",
            "serviceLabel" => "FedEx 2Day®",
            "serviceCode" => "fedex_two_day",
            'packageTypeCode' => 'fedex_custom_package',
            "currency" => "USD",
            "totalAmount" => 0,
            "baseAmount" => 0,
            "isReady" => false,
            "logo" => "/partner-imgs/fedex-logo.png"
        ],
        [
            "service_id" => 5,
            "carrierCode" => "fedex",
            "carrierLabel" => "fedex",
            "serviceLabel" => "FedEx Express Saver®",
            "serviceCode" => "fedex_express_saver",
            'packageTypeCode' => 'fedex_custom_package',
            "currency" => "USD",
            "totalAmount" => 0,
            "baseAmount" => 0,
            "isReady" => false,
            "logo" => "/partner-imgs/fedex-logo.png"
        ],
        [
            "service_id" => 6,
            "carrierCode" => "fedex",
            "carrierLabel" => "fedex",
            "serviceLabel" => "FedEx Ground®",
            "serviceCode" => "fedex_ground",
            'packageTypeCode' => 'fedex_custom_package',
            "currency" => "USD",
            "totalAmount" => 0,
            "baseAmount" => 0,
            "isReady" => false,
            "logo" => "/partner-imgs/fedex-logo.png"
        ],
        [
            "service_id" => 7,
            "carrierCode" => "fedex",
            "carrierLabel" => "fedex",
            "serviceLabel" => "FedEx Home Delivery®",
            "serviceCode" => "fedex_ground_home_delivery",
            'packageTypeCode' => 'fedex_custom_package',
            "currency" => "USD",
            "totalAmount" => 0,
            "baseAmount" => 0,
            "isReady" => false,
            "logo" => "/partner-imgs/fedex-logo.png"
        ],
        [
            "service_id" => 8,
            "carrierCode" => "fedex",
            "carrierLabel" => null,
            "serviceLabel" => "FedEx International Priority®",
            "serviceCode" => "fedex_international_priority",
            'packageTypeCode' => 'fedex_custom_package',
            "currency" => "USD",
            "totalAmount" => 0,
            "baseAmount" => 0,
            "isReady" => false,
            "logo" => "/partner-imgs/fedex-logo.png"
        ],
        [
            "service_id" => 9,
            "carrierCode" => "fedex",
            "carrierLabel" => null,
            "serviceLabel" => "FedEx International Priority Connect Plus",
            "serviceCode" => "fedex_international_connect_plus",
            'packageTypeCode' => 'fedex_custom_package',
            "currency" => "USD",
            "totalAmount" => 0,
            "baseAmount" => 0,
            "isReady" => false,
            "logo" => "/partner-imgs/fedex-logo.png"
        ],
        [
            "service_id" => 10,
            "carrierCode" => "ups",
            "carrierLabel" => "ups",
            "serviceLabel" => "UPS® Standard",
            "serviceCode" => "ups_standard",
            'packageTypeCode' => 'ups_custom_package',
            "currency" => "USD",
            "totalAmount" => 0,
            "baseAmount" => 0,
            "isReady" => false,
            "logo" => "/partner-imgs/ups-logo.png"
        ],
        [
            "service_id" => 11,
            "carrierCode" => "ups",
            "carrierLabel" => "ups",
            "serviceLabel" => "UPS Worldwide Express®",
            "serviceCode" => "ups_worldwide_express",
            'packageTypeCode' => 'ups_custom_package',
            "currency" => "USD",
            "totalAmount" => 0,
            "baseAmount" => 0,
            "isReady" => false,
            "logo" => "/partner-imgs/ups-logo.png"
        ],
        [
            "service_id" => 12,
            "carrierCode" => "ups",
            "carrierLabel" => "ups",
            "serviceLabel" => "UPS Worldwide Express Plus®",
            "serviceCode" => "ups_express_plus",
            'packageTypeCode' => 'ups_custom_package',
            "currency" => "USD",
            "totalAmount" => 0,
            "baseAmount" => 0,
            "isReady" => false,
            "logo" => "/partner-imgs/ups-logo.png"
        ],
        [
            "service_id" => 13,
            "carrierCode" => "ups",
            "carrierLabel" => "ups",
            "serviceLabel" => "UPS Worldwide Saver®",
            "serviceCode" => "ups_worldwide_saver",
            'packageTypeCode' => 'ups_custom_package',
            "currency" => "USD",
            "totalAmount" => 0,
            "baseAmount" => 0,
            "isReady" => false,
            "logo" => "/partner-imgs/ups-logo.png"
        ],
        [
            "service_id" => 14,
            "carrierCode" => "ups",
            "carrierLabel" => "ups",
            "serviceLabel" => "UPS Next Day Air®",
            "serviceCode" => "ups_next_day_air",
            'packageTypeCode' => 'ups_custom_package',
            "currency" => "USD",
            "totalAmount" => 0,
            "baseAmount" => 0,
            "isReady" => false,
            "logo" => "/partner-imgs/ups-logo.png"
        ],
        [
            "service_id" => 15,
            "carrierCode" => "ups",
            "carrierLabel" => "ups",
            "serviceLabel" => "UPS 2nd Day Air®",
            "serviceCode" => "ups_second_day_air",
            'packageTypeCode' => 'ups_custom_package',
            "currency" => "USD",
            "totalAmount" => 0,
            "baseAmount" => 0,
            "isReady" => false,
            "logo" => "/partner-imgs/ups-logo.png"
        ],
        [
            "service_id" => 16,
            "carrierCode" => "ups",
            "carrierLabel" => "ups",
            "serviceLabel" => "UPS® Ground",
            "serviceCode" => "ups_ground",
            'packageTypeCode' => 'ups_custom_package',
            "currency" => "USD",
            "totalAmount" => 0,
            "baseAmount" => 0,
            "isReady" => false,
            "logo" => "/partner-imgs/ups-logo.png"
        ],
        [
            "service_id" => 17,
            "carrierCode" => "ups",
            "carrierLabel" => "ups",
            "serviceLabel" => "UPS Next Day Air Saver®",
            "serviceCode" => "ups_next_day_air_saver",
            'packageTypeCode' => 'ups_custom_package',
            "currency" => "USD",
            "totalAmount" => 0,
            "baseAmount" => 0,
            "isReady" => false,
            "logo" => "/partner-imgs/ups-logo.png"
        ],
        [
            "service_id" => 18,
            "carrierCode" => "ups",
            "carrierLabel" => "ups",
            "serviceLabel" => "UPS 2nd Day Air A.M.®",
            "serviceCode" => "ups_second_day_air_am",
            'packageTypeCode' => 'ups_custom_package',
            "currency" => "USD",
            "totalAmount" => 0,
            "baseAmount" => 0,
            "isReady" => false,
            "logo" => "/partner-imgs/ups-logo.png"
        ],
        [
            "service_id" => 19,
            "carrierCode" => "ups",
            "carrierLabel" => "ups",
            "serviceLabel" => "UPS 3 Day Select®",
            "serviceCode" => "ups_three_day_select",
            'packageTypeCode' => 'ups_custom_package',
            "currency" => "USD",
            "totalAmount" => 0,
            "baseAmount" => 0,
            "isReady" => false,
            "logo" => "/partner-imgs/ups-logo.png"
        ],
        [
            "service_id" => 20,
            "carrierCode" => "ups",
            "carrierLabel" => "ups",
            "serviceLabel" => "UPS Worldwide Expedited®",
            "serviceCode" => "ups_worldwide_expedited",
            'packageTypeCode' => 'ups_custom_package',
            "currency" => "USD",
            "totalAmount" => 0,
            "baseAmount" => 0,
            "isReady" => false,
            "logo" => "/partner-imgs/ups-logo.png"
        ]
    ];

    public function checkAuth()
    {
        if (Auth::check()) {
            if (Auth::user()->type != 'admin') {
                return response()->json([
                    'init' => true,
                ]);
            } else {
                return response()->json([
                    'init' => false,
                ]);
            }
        } else {
            return response()->json([
                'init' => true,
            ]);
        }
    }

    public function dashboard(Request $request)
    {
        $status = $request->has('status') ? $request->status : 'packages';

        $query = Package::with('customer', 'warehouse', 'child_packages');

        if ($status == 'rejected') {
            $query->where('status', 'rejected');
        } else {
            $query->where('status', '<>', 'rejected');
        }

        if (Auth::user()->type == 'customer') {
            $query->where('customer_id', Auth::user()->id);
        }

        if (!empty($request->suite_no)) {
            $suite_no = intval($request->suite_no) - 4000;
            $query->whereHas('customer', function ($query) use ($suite_no) {
                $query->where('id', $suite_no);
            });
        }

        $packages = $query->orderBy('id', 'desc')->paginate(25);

        return Inertia::render('Dashboard', [
            'pkgs' => $packages,
            'filter' => [
                'status' => $status
            ]
        ]);
    }

    public function pricingTable()
    {
        $services = ServicePage::all();

        return Inertia::render('PricingTable', ['services' => $services]);
    }

    public function pricing()
    {

        $countries = Country::all(['id', 'nicename as name', 'iso'])->toArray();

        $warehouses = Warehouse::all()->toArray();

        return Inertia::render('Pricing', [
            'countries' => $countries,
            'warehouses' => $warehouses,
            'services' => $this->service_list
        ]);
    }

    public function getServicesList()
    {
        return response()->json(['services' => $this->service_list]);
    }


    public function shopping()
    {


        $user = Auth::user();

        $profile_complete = TRUE;
        //check if profile is complete
        if (
            empty($user->first_name) ||
            empty($user->last_name) ||
            empty($user->address1) ||
            empty($user->country) ||
            empty($user->state) ||
            empty($user->city) ||
            empty($user->postal_code) ||
            empty($user->phone_no) ||
            empty($user->email)
        ) {
            $profile_complete = FALSE;
        }

        $cities_from = City::where('country_code_2', 'US')->get()->toArray();
        $cities_to = City::where('country_code_2', '!=', 'US')->get()->toArray();


        return Inertia::render('Shopping', [
            'cities_from' => $cities_from,
            'cities_to' => $cities_to,
            'profile_complete' => $profile_complete
        ]);
    }

    public function getQuote(Request $request)
    {
        $service = $request->get('service');

        $service = json_decode($service);
        // dd($service);

        $rules = [
            'weight' => ['required', 'gt:0'],
            'length' => ['required', 'gt:0'],
            'width' => ['required', 'gt:0'],
            'height' => ['required', 'gt:0'],
        ];

        $validator = Validator::make($request->all(), $rules, $message = []);

        if ($validator->fails()) {
            return response()->json([
                'status' => TRUE,
                'service' => [],
                'errors' => $validator->errors()->all(),
            ]);
        }


        $markup = SiteSetting::getByName('markup');

        $ship_from = $request->input('ship_from');
        $ship_to = $request->input('ship_to');
        $weight = $request->input('weight');
        $units = $request->input('weight_unit');
        $length = $request->input('length');
        $width = $request->input('width');
        $height = $request->input('height');
        $zipcode = $request->input('zipcode');
        $cityName = $request->has('city') ? $request->city : null;
        $is_residential = $request->input('is_residential') == 1 ? true : false;


        //$declared_value = $request->input('declared_value');

        $declared_value = '10.0';

        $warehouse = Warehouse::where('id', $ship_from)->first();

        $country = Country::where('id', $ship_to)->first();

        $units = explode('_', $units);

        $weight_unit = isset($units[0]) ? $units[0] : 'lb';
        $dimention_unit = isset($units[1]) ? $units[1] : 'in';

        $headers = [
            'cache-control' => 'no-cache',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token
        ];

        $errors = [];

        $sender = [
            "country" =>  'US',
            "zip" => '92804',
        ];

        $receiver = [
            "country" => $country->iso,
            "zip" => empty($zipcode) ? '40050' : $zipcode,
        ];

        if ($cityName != null) {
            $receiver['city'] = $cityName;
        } elseif ($country->iso == 'PK') {
            $receiver['city'] = "Lahore";
        }

        $pieces = [
            0 => [
                "weight" => $weight,
                "length" => $length,
                "width" => $width,
                "height" => $height,
                "insuranceAmount" => null,
                "declaredValue" => $declared_value
            ]
        ];

        $post_params = [
            "carrierCode" => $service->carrierCode,
            "serviceCode" =>  $service->serviceCode,
            "packageTypeCode" => $service->packageTypeCode,
            "sender" => $sender,
            "receiver" => $receiver,
            "residential" => $is_residential,
            "signatureOptionCode" => null,
            "contentDescription" => "stuff and things",
            "weightUnit" => $weight_unit,
            "dimUnit" => $dimention_unit,
            "currency" => "USD",
            "customsCurrency" => "USD",
            "pieces"  => $pieces,
            "billing" => [
                "party" => "sender"
            ]
        ];
        $service_rate = [];
        try {

            $client = new Client();

            $request = $client->post('https://xpsshipper.com/restapi/v1/customers/' . $this->customer_id . '/quote', [
                'headers' => $headers,
                'body' => json_encode($post_params),
                'http_errors' => true,
            ]);

            $response = $request ? $request->getBody()->getContents() : null;
            \Log::info($post_params);
            \Log::info($response);
            $response = json_decode($response);

            $markup_amount = $response->totalAmount * ((int)$markup / 100);

            $total = $response->totalAmount + $markup_amount;

            $total = number_format($total, 2);

            $service_rate = [
                "service_id" => $service->service_id,
                "carrierCode" => $service->carrierCode,
                'serviceLabel' => $service->serviceLabel,
                'serviceCode' => $service->serviceCode,
                "packageTypeCode" => $service->packageTypeCode,
                'currency' => $response->currency,
                'totalAmount' => $total,
                'baseAmount' => $response->baseAmount,
                'isReady' => true,
                'logo' => $service->logo,
                'markup_fee' => $markup_amount,
            ];
        } catch (\Exception $ex) {

            $ex_message = $ex->getMessage();

            $pos = strpos($ex_message, '{"error":"');

            $pos1 = strpos($ex_message, '"errorCategory"');
            $length = $pos1 - ($pos + 12);

            $message = substr($ex_message, $pos + 10, $length);

            $errors[] = [
                'label' => $service->serviceLabel,
                'serviceCode' => $service->serviceCode,
                'message' => $message,
                'details' => $ex_message,
            ];
        }

        return response()->json([
            'status' => TRUE,
            'service' => $service_rate,
            'errors' => $errors,
        ]);
    }

    /**
     * Not used now, using getQuote for single service at atime.
     */

    public function getQuotes(Request $request)
    {

        $markup = SiteSetting::getByName('markup');
        $ship_from = $request->input('ship_from');
        $ship_to = $request->input('ship_to');
        $weight = $request->input('weight');
        $units = $request->input('weight_unit', 'lb_in');
        $length = $request->input('length');
        $width = $request->input('width');
        $height = $request->input('height');
        $zipcode = $request->input('zipcode');
        //$declared_value = $request->input('declared_value');
        $declared_value = '1.0';
        $warehouse = Warehouse::where('id', $ship_from)->first();
        $country = Country::where('id', $ship_to)->first();
        $units = explode('_', $units);
        $weight_unit = isset($units[0]) ? $units[0] : 'lb';
        $dimention_unit = isset($units[1]) ? $units[1] : 'in';
        $headers = [
            'cache-control' => 'no-cache',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token
        ];
        $client = new Client();

        $request = $client->get('https://xpsshipper.com/restapi/v1/customers/' . $this->customer_id . '/services', [
            'headers' => $headers,
            'http_errors' => true,
        ]);

        $response = $request ? $request->getBody()->getContents() : null;
        $response = json_decode($response);

        echo '<pre>';
        print_r($response->services);
        exit;

        $service_rates = [];
        $errors = [];
        $sender = [
            "country" =>  'US',
            "zip" =>  $warehouse->zip
        ];
        $receiver = [
            "country" => $country->iso,
            "zip" => $zipcode,
        ];
        $pieces = [
            0 => [
                "weight" => $weight,
                "length" => $length,
                "width" => $width,
                "height" => $height,
                "insuranceAmount" => null,
                "declaredValue" => $declared_value
            ]
        ];
        foreach ($response->services as $service) {
            if (!empty($service->carrierCode) && strpos($service->serviceCode, 'international') !== false) {

                $post_params = [
                    "carrierCode" => $service->carrierCode,
                    "serviceCode" =>  $service->serviceCode,
                    "packageTypeCode" => $this->getPackageTypeCode($service),
                    "sender" => $sender,
                    "receiver" => $receiver,
                    "residential" => true,
                    "signatureOptionCode" => "DIRECT",
                    "contentDescription" => "stuff and things",
                    "weightUnit" => $weight_unit,
                    "dimUnit" => $dimention_unit,
                    "currency" => "USD",
                    "customsCurrency" => "USD",
                    "pieces"  => $pieces,
                    "billing" => [
                        "party" => "receiver"
                    ]
                ];
                try {
                    $request = $client->post('https://xpsshipper.com/restapi/v1/customers/' . $this->customer_id . '/quote', [
                        'headers' => $headers,
                        'body' => json_encode($post_params),
                        'http_errors' => true,
                    ]);

                    $response = $request ? $request->getBody()->getContents() : null;
                    $response = json_decode($response);
                    $markup_amount = $response->totalAmount * ((int)$markup / 100);
                    $total = $response->totalAmount + $markup_amount;
                    $total = number_format($total, 2);
                    $service_rates[] = [
                        'service_id' => $service->service_id,
                        'label' => $service->serviceLabel,
                        'serviceCode' => $service->serviceCode,
                        'currency' => $response->currency,
                        //'totalAmount' => $response->totalAmount,
                        'totalAmount' => $total,
                        'baseAmount' => $response->baseAmount,
                    ];
                } catch (\Exception $ex) {

                    $ex_message = $ex->getMessage();
                    $pos = strpos($ex_message, '{"error":"');

                    $pos1 = strpos($ex_message, '"errorCategory"');
                    $length = $pos1 - ($pos + 12);

                    $message = substr($ex_message, $pos + 10, $length);

                    $errors[] = [
                        'label' => $service->serviceLabel,
                        'serviceCode' => $service->serviceCode,
                        'message' => $message,
                    ];
                }
            }
        }

        return response()->json([
            'status' => TRUE,
            'services' => $service_rates,
            'errors' => $errors,
        ]);
    }


    public function getQuoteByOrders(Request $request)
    {
        $service = $request->get('service');

        $service = json_decode($service);


        $piecesStrings = $request->pieces;
        $pieces = [];
        foreach ($piecesStrings as $strValue) {
            $pieces[] = json_decode($strValue, true);
        }



        $markup = SiteSetting::getByName('markup');

        $ship_from = $request->input('ship_from');
        $ship_to = $request->input('ship_to');
        $units = $request->input('weight_unit');
        $length = $request->input('length');
        $zipcode = $request->input('zipcode');
        $cityName = $request->has('city') ? $request->city : null;
        $is_residential = $request->input('is_residential') == 1 ? true : false;


        //$declared_value = $request->input('declared_value');

        $declared_value = '10.0';

        $warehouse = Warehouse::where('id', $ship_from)->first();

        $country = Country::where('id', $ship_to)->first();

        $units = explode('_', $units);

        $weight_unit = isset($units[0]) ? $units[0] : 'lb';
        $dimention_unit = isset($units[1]) ? $units[1] : 'in';

        $headers = [
            'cache-control' => 'no-cache',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token
        ];

        $errors = [];

        $sender = [
            "country" =>  'US',
            "zip" => '92804',
        ];

        $receiver = [
            "country" => $country->iso,
            "zip" => empty($zipcode) ? '40050' : $zipcode,
        ];

        if ($cityName != null) {
            $receiver['city'] = $cityName;
        } elseif ($country->iso == 'PK') {
            $receiver['city'] = "Lahore";
        }


        $post_params = [
            "carrierCode" => $service->carrierCode,
            "serviceCode" =>  $service->serviceCode,
            "packageTypeCode" => $service->packageTypeCode,
            "sender" => $sender,
            "receiver" => $receiver,
            "residential" => $is_residential,
            "signatureOptionCode" => null,
            "contentDescription" => "stuff and things",
            "weightUnit" => $weight_unit,
            "dimUnit" => $dimention_unit,
            "currency" => "USD",
            "customsCurrency" => "USD",
            "pieces"  => $pieces,
            "billing" => [
                "party" => "sender"
            ]
        ];
        $service_rate = [];
        try {

            $client = new Client();

            $request = $client->post('https://xpsshipper.com/restapi/v1/customers/' . $this->customer_id . '/quote', [
                'headers' => $headers,
                'body' => json_encode($post_params),
                'http_errors' => true,
            ]);

            $response = $request ? $request->getBody()->getContents() : null;
            \Log::info($post_params);
            \Log::info($response);
            $response = json_decode($response);

            $markup_amount = $response->totalAmount * ((int)$markup / 100);

            $total = $response->totalAmount + $markup_amount;

            $total = number_format($total, 2);

            $service_rate = [
                "service_id" => $service->service_id,
                "carrierCode" => $service->carrierCode,
                'serviceLabel' => $service->serviceLabel,
                'serviceCode' => $service->serviceCode,
                "packageTypeCode" => $service->packageTypeCode,
                'currency' => $response->currency,
                'totalAmount' => $total,
                'baseAmount' => $response->baseAmount,
                'isReady' => true,
                'logo' => $service->logo,
                'markup_fee' => $markup_amount,
            ];
        } catch (\Exception $ex) {

            $ex_message = $ex->getMessage();

            $pos = strpos($ex_message, '{"error":"');

            $pos1 = strpos($ex_message, '"errorCategory"');
            $length = $pos1 - ($pos + 12);

            $message = substr($ex_message, $pos + 10, $length);

            $errors[] = [
                'label' => $service->serviceLabel,
                'serviceCode' => $service->serviceCode,
                'message' => $message,
                'details' => $ex_message,
            ];
        }

        return response()->json([
            'status' => TRUE,
            'service' => $service_rate,
            'errors' => $errors,
        ]);
    }

    public function putTestOrder()
    {
        $headers = [
            'cache-control' => 'no-cache',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token
        ];

        $post_params = array(
            'orderId' => 'OrderTest005',
            'orderDate' => '2022-10-14',
            'orderNumber' => 'Test Order 005',
            'fulfillmentStatus' => 'pending',
            'shippingService' => 'fedex custom package',
            'shippingTotal' => '8.24',
            'weightUnit' => 'lb',
            'dimUnit' => 'in',
            'dueByDate' => '2022-10-26',
            'orderGroup' => 'Workstation 1',
            'contentDescription' => 'Stuff and things',
            'sender' =>
            array(
                'name' => 'Habibur Haseeb',
                'company' => 'Aman & Baida Enterprise',
                'address1' => '3578 West Savanna St',
                'address2' => '',
                'city' => 'Anaheim',
                'state' => 'CA',
                'zip' => '92804',
                'country' => 'US',
                'phone' => '2097517988',
                'email' => 'habib362@gmail.com',
            ),
            'receiver' =>
            array(
                'name' => 'Samia Khan',
                'company' => '',
                'address1' => 'Multan Road',
                'address2' => 'Dummy House',
                'city' => 'Lahore',
                'state' => '',
                'zip' => '84115',
                'country' => 'PK',
                'phone' => '+923004556435',
                'email' => 'samia@yopmail.coom',
            ),
            'items' =>
            array(
                0 =>
                array(
                    'productId' => '856673',
                    'sku' => 'ade3-fe21-bb9a',
                    'title' => 'Socks',
                    'price' => '3.99',
                    'quantity' => 2,
                    'weight' => '0.5',
                    'imgUrl' => 'http://sockstore.egg/img/856673',
                    'htsNumber' => '555555',
                    'countryOfOrigin' => 'US',
                    'lineId' => '1',
                ),
                1 =>
                array(
                    'productId' => '856673s',
                    'sku' => 'ade3-fe21-bb9aa',
                    'title' => 'Socsks',
                    'price' => '3.99',
                    'quantity' => 2,
                    'weight' => '1.0',
                    'imgUrl' => 'http://sockstore.egg/img/856673',
                    'htsNumber' => '555555',
                    'countryOfOrigin' => 'US',
                    'lineId' => '1',
                ),
            ),
            'packages' =>
            array(
                0 =>
                array(
                    'weight' => '0.5',
                    'length' => '6',
                    'width' => '5',
                    'height' => '2.5',
                    'insuranceAmount' => NULL,
                    'declaredValue' => NULL,
                ),
                1 =>
                array(
                    'weight' => '1.0',
                    'length' => '8',
                    'width' => '6',
                    'height' => '4',
                    'insuranceAmount' => NULL,
                    'declaredValue' => NULL,
                ),
                2 =>
                array(
                    'weight' => '3.5',
                    'length' => '10',
                    'width' => '20',
                    'height' => '15',
                    'insuranceAmount' => NULL,
                    'declaredValue' => NULL,
                ),
            ),
        );


        try {

            $client = new Client();

            $request = $client->put('https://xpsshipper.com/restapi/v1/customers/' . $this->customer_id . '/integrations/' . $this->integration_id . '/orders/' . $post_params['orderId'], [
                'headers' => $headers,
                'body' => json_encode($post_params),
                'http_errors' => true,
            ]);

            $response = $request ? $request->getBody()->getContents() : null;

            \Log::info($response);

            return $response;
        } catch (\Exception $ex) {

            $ex_message = $ex->getMessage();

            return $ex_message;
        }
    }


    private function getPackageTypeCode($service)
    {

        $type = "";

        switch ($service->carrierCode) {
            case "dhl":
                $type =  "dhl_custom_package";
                break;
            case "fedex":
                $type = "fedex_custom_package";
                break;
            case "usps":
                $type = "usps_custom_package";
                break;
        }

        return $type;
    }

    public function notifications(Request $request)
    {

        $currentPage = $request->current_page ?? 1;
        $take = 50 * $currentPage;

        $user = \auth()->user();
        $notifications = \auth()->user()->notifications()->select(DB::raw('DATE(created_at) as date'));
        $totalPage = ceil($notifications->count() / 50);


        $notifications = $notifications->take($take)->pluck('date')->toArray();


        $notifications = array_unique($notifications);

        foreach ($notifications as $date) {
            $notifies = $user->notifications()->whereDate('created_at', $date)->orderby('created_at', 'desc')->get();
            $notifications = [];

            foreach ($notifies as $notification) {
                $notifications[] = [
                    'id' => $notification->id,
                    'created_at' => Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', strtotime($notification->created_at)))->diffForHumans(),
                    'read_at' => $notification->read_at != NULL ? date('d-m-Y h:m a', strtotime($notification->read_at)) : NULL,
                    'content' => $notification->data['message'] ?? null
                ];
            }

            $data[] = [
                'date' => ($date == Carbon::now()->format('Y-m-d')) ? "Today" : date('F  j, Y', strtotime($date)),
                'notifications' => $notifications
            ];
        }

        $notificationData = collect($data);

        if ($request->has('current_page')) {
            return response()->json([
                'notifications' => $notificationData,
                'totalPage' => $totalPage ?? 1,
                'currentPage' => $currentPage ?? 1
            ]);
        }

        return Inertia::render('Notifications', [
            'notifications' => $notificationData,
            'totalPage' => $totalPage ?? 1,
            'currentPageProp' => $currentPage ?? 1
        ]);
    }

    public function markAllRead()
    {
        \auth()->user()->unreadNotifications->markAsRead();
        return redirect('notifications');
    }


    public function markRead(Request $request)
    {

        $id = $request->input('notification_id');

        $notification = auth()->user()->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();
        }

        return response()->json([
            'status' => 1,
            'message' => "Read successfully",
        ]);
    }

    public function getMailingAddress()
    {
        $response['warehouses'] = Warehouse::where('id',2)->get();

        return \response()->json([
            'status' => true,
            'data' => $response['warehouses'],
        ]);
    }

    public function announcement()
    {
        $customers = User::orderBy('id', 'asc')->where('type', 'customer')->get();
        Notification::send($customers, new AnnouncementNotification());
    }

    public function decodePdf()
    {
        $base64EncodedPDF = "JVBERi0xLjQKMSAwIG9iago8PAovVHlwZSAvQ2F0YWxvZwovUGFnZXMgMyAwIFIKPj4KZW5kb2JqCjIgMCBvYmoKPDwKL1R5cGUgL091dGxpbmVzCi9Db3VudCAwCj4+CmVuZG9iagozIDAgb2JqCjw8Ci9UeXBlIC9QYWdlcwovQ291bnQgMQovS2lkcyBbMTggMCBSXQo+PgplbmRvYmoKNCAwIG9iagpbL1BERiAvVGV4dCAvSW1hZ2VCIC9JbWFnZUMgL0ltYWdlSV0KZW5kb2JqCjUgMCBvYmoKPDwKL1R5cGUgL0ZvbnQKL1N1YnR5cGUgL1R5cGUxCi9CYXNlRm9udCAvSGVsdmV0aWNhCi9FbmNvZGluZyAvTWFjUm9tYW5FbmNvZGluZwo+PgplbmRvYmoKNiAwIG9iago8PAovVHlwZSAvRm9udAovU3VidHlwZSAvVHlwZTEKL0Jhc2VGb250IC9IZWx2ZXRpY2EtQm9sZAovRW5jb2RpbmcgL01hY1JvbWFuRW5jb2RpbmcKPj4KZW5kb2JqCjcgMCBvYmoKPDwKL1R5cGUgL0ZvbnQKL1N1YnR5cGUgL1R5cGUxCi9CYXNlRm9udCAvSGVsdmV0aWNhLU9ibGlxdWUKL0VuY29kaW5nIC9NYWNSb21hbkVuY29kaW5nCj4+CmVuZG9iago4IDAgb2JqCjw8Ci9UeXBlIC9Gb250Ci9TdWJ0eXBlIC9UeXBlMQovQmFzZUZvbnQgL0hlbHZldGljYS1Cb2xkT2JsaXF1ZQovRW5jb2RpbmcgL01hY1JvbWFuRW5jb2RpbmcKPj4KZW5kb2JqCjkgMCBvYmoKPDwKL1R5cGUgL0ZvbnQKL1N1YnR5cGUgL1R5cGUxCi9CYXNlRm9udCAvQ291cmllcgovRW5jb2RpbmcgL01hY1JvbWFuRW5jb2RpbmcKPj4KZW5kb2JqCjEwIDAgb2JqCjw8Ci9UeXBlIC9Gb250Ci9TdWJ0eXBlIC9UeXBlMQovQmFzZUZvbnQgL0NvdXJpZXItQm9sZAovRW5jb2RpbmcgL01hY1JvbWFuRW5jb2RpbmcKPj4KZW5kb2JqCjExIDAgb2JqCjw8Ci9UeXBlIC9Gb250Ci9TdWJ0eXBlIC9UeXBlMQovQmFzZUZvbnQgL0NvdXJpZXItT2JsaXF1ZQovRW5jb2RpbmcgL01hY1JvbWFuRW5jb2RpbmcKPj4KZW5kb2JqCjEyIDAgb2JqCjw8Ci9UeXBlIC9Gb250Ci9TdWJ0eXBlIC9UeXBlMQovQmFzZUZvbnQgL0NvdXJpZXItQm9sZE9ibGlxdWUKL0VuY29kaW5nIC9NYWNSb21hbkVuY29kaW5nCj4+CmVuZG9iagoxMyAwIG9iago8PAovVHlwZSAvRm9udAovU3VidHlwZSAvVHlwZTEKL0Jhc2VGb250IC9UaW1lcy1Sb21hbgovRW5jb2RpbmcgL01hY1JvbWFuRW5jb2RpbmcKPj4KZW5kb2JqCjE0IDAgb2JqCjw8Ci9UeXBlIC9Gb250Ci9TdWJ0eXBlIC9UeXBlMQovQmFzZUZvbnQgL1RpbWVzLUJvbGQKL0VuY29kaW5nIC9NYWNSb21hbkVuY29kaW5nCj4+CmVuZG9iagoxNSAwIG9iago8PAovVHlwZSAvRm9udAovU3VidHlwZSAvVHlwZTEKL0Jhc2VGb250IC9UaW1lcy1JdGFsaWMKL0VuY29kaW5nIC9NYWNSb21hbkVuY29kaW5nCj4+CmVuZG9iagoxNiAwIG9iago8PAovVHlwZSAvRm9udAovU3VidHlwZSAvVHlwZTEKL0Jhc2VGb250IC9UaW1lcy1Cb2xkSXRhbGljCi9FbmNvZGluZyAvTWFjUm9tYW5FbmNvZGluZwo+PgplbmRvYmoKMTcgMCBvYmogCjw8Ci9DcmVhdGlvbkRhdGUgKEQ6MjAwMykKL1Byb2R1Y2VyIChGZWRFeCBTZXJ2aWNlcykKL1RpdGxlIChGZWRFeCBTaGlwcGluZyBMYWJlbCkNL0NyZWF0b3IgKEZlZEV4IEN1c3RvbWVyIEF1dG9tYXRpb24pDS9BdXRob3IgKENMUyBWZXJzaW9uIDUxMjAxMzUpCj4+CmVuZG9iagoxOCAwIG9iago8PAovVHlwZSAvUGFnZQ0vUGFyZW50IDMgMCBSCi9SZXNvdXJjZXMgPDwgL1Byb2NTZXQgNCAwIFIgCiAvRm9udCA8PCAvRjEgNSAwIFIgCi9GMiA2IDAgUiAKL0YzIDcgMCBSIAovRjQgOCAwIFIgCi9GNSA5IDAgUiAKL0Y2IDEwIDAgUiAKL0Y3IDExIDAgUiAKL0Y4IDEyIDAgUiAKL0Y5IDEzIDAgUiAKL0YxMCAxNCAwIFIgCi9GMTEgMTUgMCBSIAovRjEyIDE2IDAgUiAKID4+Ci9YT2JqZWN0IDw8IC9GZWRFeEV4cHJlc3MgMjAgMCBSCi9FeHByZXNzRSAyMSAwIFIKL2JhcmNvZGUwIDIyIDAgUgovRW5nbGlzaEZvbGRpbmdFbmdsaXNoLnBuZzI3MCAyMyAwIFIKL0VuZ2xpc2hUYW5kQ19VU19Eb21FeHByZXNzRW5nbGlzaC5wbmcyNzAgMjQgMCBSCj4+Cj4+Ci9NZWRpYUJveCBbMCAwIDc5MiA2MTJdCi9UcmltQm94WzAgMCA3OTIgNjEyXQovQ29udGVudHMgMTkgMCBSCi9Sb3RhdGUgOTA+PgplbmRvYmoKMTkgMCBvYmoKPDwgL0xlbmd0aCAzOTc5Ci9GaWx0ZXIgWy9BU0NJSTg1RGVjb2RlIC9GbGF0ZURlY29kZV0gCj4+CnN0cmVhbQpHYXQ9L2dRIThfJlVlIk9zM08jO0NgbUp0R2MwRDEyJ19LSmxlVGgyWERBK0clKik2YyNGPUdsODdMWkdGJUgiSDx0P2FEZmImPmwxT1pqNwo9Ti5rVT02V1JSSklhY0hRQldKTVslZUo2VUpaOEAoPSYkdVVeKUlPX29LNSZRUFpbMzdrNzQrS1tCZkNJI04kNS9BJGFqKWhsOitlZ2I9JgpPckAtZUpFJ188U1xLQW9dUWotSiVYcEQ6bUQ+Ok1wdHEhXCMoLWYuQzY6bXNPaUQmXEJvakMzX3N1UiVuQ2RAMjpPaTA7cGxDP2huTyxbOwpPMktaYjgmdFNSTzI0PDlLL1BiLTQzMTczU1ozVCErbk9gcj0zYlVLYyFbOT5vRE9IRnJTPV42Ki1dYSFgUF8rPVdsSGh0Rl5qLj0zLkNQYwpwakJrJWAsTC8jMycmJ1RGUEhiXCNFXG49P2Mya1RjL3BLbFVqWmsoMkZvZmplVXVHWlxhRllwVCs+Y09YW1xkXCUqTjQ2PFllYktNPXJtMworM1BnJyotXGtUJ11lcHQpM1dHQzViKTtxXS91VUo8Jz0/RDtDQ3IpTF1TOyYxOiUmai47MzFIcVBCNi5jOls3PyoqcFNiJztKPzxHXipNdQplZWZIbmpcPSNvbk86WFE5WlMyQTRwSV1IKiowaUsuRCJFJWlWLTxMZEEwSygpYFdHVE5kTGNtclZrQEhCJVYkb15PM1FKRjtVL2BXSyE0Xgo3XGdiSTNTViQ4T1ZIXSU0cDRBTz8xPlI8PnRYKEErTS05M21Pb1tNTFRBWy5cKkpqRCRDUy5dYHBgVVtZSi5fNDRHbWhaN11fTGleKUBLTgpmOi84IWMvWmxXIzxqLF9jLT5CY1wsWiJyNyo8Xzg6MWo9Xm43IjBWbCVPUiNUOkRcOWlGW1NwRDI2IkBnWXVYZGtWLTFXJFg7dXRAXkYmQQpDNjZpJVVAQE8yayYpaUtrXHEtNFFtLFUmSnQxRTZfOWU/bURVMjQ5V1w+OC5ELjwiUy5uQ0hbLENRPF1ub2smImlQakAwaUcqYD5icDFucApRaWxXbUVIP1E8XW1ecTVDYzZWWDFCNjBCXTduRUxEZjpCdHJWbEtYT2ViYiUnJzktWj90Tm5TbVBhZ1dGdVEobVFvRTpJaGZeZG4vN2AjdAotcW1fYSwmPFxvNiFocS1vYWoxWkMrY3JKXnViUmxIOHQ4L1tfKUZjSVUzXl5NSlQ+JDNhInQlNjMqUjYuPCIuKitlVDwiPFYqQVc9aE9LQwosZi1kSGNHaDReIkBAM0RlNnFsYyEwX28rVEQ7LGtTcVlFYVNMcDItI1w3NSwjOHVEWiJJO1prOW0hbj4jQThaK1hFUSZgYilzc2NwY2hQLApiWiQ5KDFRV2JQTD9wMkdgcjwsdTs7KU0wSVdCRlFFXkFsZU4uYjVqL21YM19WLDBwU0tzNi9LNjhkaz42QyJOJTE2InFbQXUocTUtJGE5WgpCNydtJGwwOFcxT1IjSmhYV1ZrSj1CT2JvVzMqNCRVSiFvdTBkRk1dQiJlb0NUUyhATSNbVVo7NmBjU2RcY1FVc2xZNXVfS10kOVQpPD0rYwo2NjEvKz9ValAjWTtBJUBKVFIhLUVPRk1qWGFvLEk2KURYbDVhWGciZ3FtKTcqRnM+LjNoVkpBY0VVQSQrXG02aW4kcUdwZl1rcW84akkiago5TUs+Jy5wMVAhOD04YE8nTFkyYGtwN10uWV1idGYjYyE3ZjY9cVBab1NXKVdDN0M5Z2ZxbF9bbCpSLzpIN2pSa2V0RmRAbXVWRWI1LEplNApxOUddLUhEJScvKlsrNkc7M3J1SicnZyopUmFFQiIzR0QlKEcpJFlGPUdIbydOI28wNFBvdGh1LyZEQVIrQjI5LFZJRj8pIjVpNWs9OlpTIQoqcG9hTyVNdChJUz9UZGkjWjZqNl5KSU90OyskcGRLSSwyOlcoM0UuZGEiI0FoSy1POGxRUG4xPidzbT1kWzY+SjsrZHBCZCY8KGlTTkknbQpOVEgnckBRZkJDPE0wQ08nJDZMJCo5ZVM1W1NDXzZNMyZxcjpaTjdsKkhjQWdQTz83OkMpbjkxWUpgSnIlZitnS18sMWBbNj5HVjYtLllCRwo6bG4sXjs0XCpMXChZRmRYTV1fOmsyJ0NhMkBIIStrPCJHW2tgRSxsaFxNSD08Y2RkPFcvIixcVC5nPjs0VUs8Ti8kVS1eVDUlVnEnP1w3SgpOUj06PypGcUl1SCYrYVdDJGIyZjUsX2pQS0pwSD1fLU9uXSpjKSMwb09gcm1TYWxuRT5DbDspRl50WWtWZExyaWM0YjImazBQYC06MDpuQApEakVyTDUiWWo4LGFvTGpxOUdaRDNoZUBpRT8pJnAtbnI0NmxDaWpQaiZOL0woV2xgXERNYmNmPTQvb2laUzdNLipNPkUkSlRWTXI0bkEwNwotaFJodFRzRjs4algpbCQ7YzlcJiRgbnRXTSxFQjNlMnJvJ05wWTEuWEAkUEVtU28tK09zcCEiYUUsMyJDQm9cM0duL2JDMEdgIW81JDI4NwohWztRXGFvWy5TVV5jKWMkYExzbmc1UyNIQG8vKCVjU0trTmxRQVVIOjBHRm8rJVcpYEE6dThbVHMxZjRZJkhaMVY3Ymk5az43NGVANVIzbAouLnNaMC1LQklXZWZnWWNmZGZtcT1CbzREQCM5InNMbjZmdC0rRyo2KFc/LXBUYXBsPEFadFMtQyE3dFg/MywxPkxUSSRuKG9WXlovRWcvXwoia0ghJzc8VCUsaUdAU2MhdT1eZ1xtQXM/I2ZuWG5uPicjSypCPUZjR1QndW5XQkwwPUhYP2VyLiNZSScoLDAtR0xeYXJrLyJkQWBLPzZQcQo1V3VSRFdfOC9zVjdCYmRCOlEjYiZGNHJZS1lfcz9VcjVCbjQnayZZOkJzSi9APFhZbEdjbyFNKjImbzsqUU8iWFxAXllVaFg/Tl8lKEVFYQpwR190Tj0ydXBnPCtzQ244QGYoXVRdMzYpSSJaT1s/dSQwaUpuWUhaSlcvdSVhImRORDxXIVFbPzk6a09DSy9FLDpzQWQvO1g6JT1pVU8vLgo0XkItVEpCKFo4Z25bbmdwbnUkdFdGZTZKPyhER1ZRMHBfTzxxRzhGQWlLKFs/ZDZWc3MxQjFYUV4tdWwhalRSJW1BWS9nLXVWVHJuM01AVgosIzBcPUdfYDMzSUMmZmZwWS4+Q2h1PEs8WWwvUDJrSydMRGAwTDxGWGlvOWE1PkNBQipXaXIuOz9fKHFWMV81b1owWihrPUliNWRQTk1WXgpOQCsxb1cwPls1KUNWRFU4L19aOUw1TSxWWU1Yai1pUFYubyw0ZUpUMUErTU8pIlc7KFxqSCZkIylyMDtwXj0oKkkrWU9qL2MrKTYtVkk8UwojJUMiTy0hSHJRaXQlPEwuUToqSC8uIS5kVnRVU1U8P15wU3IvITplP3FrYE8zZ0hGQW8sNW0sJEReN2pCZl5fakRoPkFYLzhRJldkRkdecApCXnRfX2NYbiEpJm41cl9fZUZVTHJqSyVyOmZCPkBxKW83MTA7cEV1UltKbDVMOityZTlPczNqa1cuIWFaQl9AUmMpISptQ1FKRF47JnAhPQo9JGdiQHJLTz0wJ0hOPVNrWHFkJlNZXEdcJl0qQlhlO1hpQSJfcFlCRm5RIys9bUtSJVljbmo1Q0U5WDEsXXQ0ND1XLTUyKiZDXygyOyprVApxU0g5Ijl0UXNSLVVRZEswSHJ0ZkpYVWFEbUpHKUNjcENmIy9SPXBqaCRCU3QkMExBZC0uSEg1R3VkSG8lVyVBPGxWTi45Q2dROGcvXDQqUAovNlhoR1dCYTFxSCc+UUMrbVcjO0kmIWAtRCoiOihNVy5mLlJNXTw6Wzwpa0JJSyI+P2NlJ15EZSlzT1AtO1RfQ2phcVNKZipeJCkuaDNhPQpcZThjQ2w2Yys5PDZsb1w/KmFTaUtnVmslNURQWCVDMDQrazBeJFkoJmA/LVBXU2kuTWVmZ14uLDxdInMwVHVzLyc6KWAqVSFJV284V09AUQpTNik1YmAwcVU+YlhuLXVzKkxeW3BaMz1FKDZGRHAmazV1NEQjb1EpJyNuIV5EI3BLUzYqNCtWSFA+MC0uU3BBQGIvRkosaitFU2JZQWFbMgorPXA4bihoJjdZRzpTaSJIN0AqQG9JP2ZUMys3WSQ9JSNiPS5IUmRKYi01cCYkSitAJzM9XzlEbFh0cTEoOUNuXCRfalVVcSdLYCsmQys/QgoqSFlxbDZCKDpPQyRNXlYsR0JnbkshY0NyWDZFL0Jqc2VCL1lmLDhMbERfPkNiMlAuR20wcjNPUlMkPChLZCs2UVtwZ2ttW14nTGsqQCddSQpGQzpXQktVUVtiRktRbXJbSEpBSGhsJy9HWGZcI0ptYSlIN0BZIW9nXihUYllmJm5NVnFgKnFLRlsuZ2NXTlZTNGRccWRjLEQiLi1uUHVlTwo/WkdxXXBlJVg0QkAjXilOMzddYkNmQWslJixOKWdFXmEmPW1JYlZARVM8YF1fM3RHOkMiMFoqcjRVR2w6a3M7RSVsK3UzW0lwUmlXLm0xSwpvLS9PN1lzSVchKm0rMSw9QmUlb0VyQk5WRS5YOW5eJmklPkxTJzxmLUQzRkk/QytdY1teSkE8UHI5LTMmUUc6JkpELlNoa3BiaV4/dU5gIQpeJ2BGVzMjcyNCTStWIWsrKy5MTS0uWHRdWVcyaWo+PDknJVEsRzFUTFAvVXJNMjc8PGZNcl1qQCcoMFssOjU1MlRULyM8OyRDXWxMU2QnLgpLWEZdTGxoVHNIX3FPKkVWNUpKKj5nYl9OZVpdTWFJRDBVKFNLIThkTiJfTFo7byFZNSojSUg5UilnNDEhaWZbJ08tKnNuKiZyVFAuVzNULgokRTEnW1xYWy1oOzVmOTlFazI8ZUNITUxtTG0tREU8Q3BBRmc6RiJ0Olh0LUlrXi83SD5lMTluZWxHTko6aFNjPGgxZmlMMjY8ZFxGUTcqXwotb2tYazBnNj0/SDVycVBVZjMvZzo2VDtTcGhfRm4vdFFHQk5jVFwvXGhxdSZJRj5vOnFfN1QhK21QNGpUXCtzJE0vNyowcU1vVyczYHIhIwpsRDZOP3FdQz1lbC48XGJyOic8SVRBS1QtckdWVnFySyRhKTE+QGsmSENvUiFwNiwnSEJlRSxfblo6ZEZuKShlUW1jIVprcDs9TWswZ2IhNApycmAhLm9GX34+CmVuZHN0cmVhbQplbmRvYmoKMjAgMCBvYmoKPDwgL1R5cGUgL1hPYmplY3QKL1N1YnR5cGUgL0ltYWdlCi9XaWR0aCAxMTgKL0hlaWdodCA0OQovQ29sb3JTcGFjZSAvRGV2aWNlR3JheQovQml0c1BlckNvbXBvbmVudCA4Ci9MZW5ndGggNDYxCi9GaWx0ZXIgWy9BU0NJSTg1RGVjb2RlIC9GbGF0ZURlY29kZV0KPj5zdHJlYW0KR2IiL2VKSV1SPyNYblhrVDZBPlhLbkRiV0IiTVpFYWpQcGQ0XScoXkBSbHQia0IiUzAkZiU/PW09NEM7Oy4kTihqUmNZUiVzZCZsOSRPdF4KKSY3PUwoNy1lcXFlQC4oL1NDOTg0LWZMWGVMWjVxY1o1bEFFQjU1PjNAMD1rIjtSaDJoQmNRTDMmSj81b1tjSDBiU0ZOXjw7O0A0MVNZOW0KYyJaST1ZbCNjNClgXyJZbG1OMyUuQS5sTlNgVmUvWTwlPy43KCgpLy5kQls7ayFKWyUnJURKXEk/K2E3OjVvTmtZXHFFUz4zPEk1YlptKEkKJWQ8QEIpOERidDNwS0djbGJRazEnaVpWczdOTTBcbyZSKnFVPmdIdS1GckNraDxrSEZHKlwkTT1fc1YvLSRcSkY6ZS8/PmZYQStDQ21wRGUKZFNQNkdjdSxcWCdBTD1hNUpDWzQ2Ry5ocDI9VTRlXiRlb1xRI2IqbTdDOCQtSkEoY1o/WWAnQzs2SnBIZVBsXCRIRUMhdV4qUWErNmdFTWsKW2ZFVipiLiM+cHI+LVdTZiM+aCZXP3Vra1RzaFlgPS9YLjY2QSo7YmlzL2RnQFFYWHNyOG07fj4KZW5kc3RyZWFtCmVuZG9iagoyMSAwIG9iago8PCAvVHlwZSAvWE9iamVjdAovU3VidHlwZSAvSW1hZ2UKL1dpZHRoIDU0Ci9IZWlnaHQgNTQKL0NvbG9yU3BhY2UgL0RldmljZUdyYXkKL0JpdHNQZXJDb21wb25lbnQgOAovTGVuZ3RoIDc3Ci9GaWx0ZXIgWy9BU0NJSTg1RGVjb2RlIC9GbGF0ZURlY29kZV0KPj5zdHJlYW0KR2IiMEpkMFRkcSRqNG9GXlUsIkhUczlFSUU7MEFULF9FKkxaJW9AN0psNVY7SCdDcz1UcnFEYUguNEJmI2M0T1ZUOyhkI2Y8R0U5fj4KZW5kc3RyZWFtCmVuZG9iagoyMiAwIG9iago8PCAvVHlwZSAvWE9iamVjdAovU3VidHlwZSAvSW1hZ2UKL1dpZHRoIDIwOQovSGVpZ2h0IDcwCi9Db2xvclNwYWNlIC9EZXZpY2VHcmF5Ci9CaXRzUGVyQ29tcG9uZW50IDgKL0xlbmd0aCAxMzUzCi9GaWx0ZXIgWy9BU0NJSTg1RGVjb2RlIC9GbGF0ZURlY29kZV0KPj5zdHJlYW0KR2IiL2NkO2dJKSNYZUZmVDRUVUFlcmFYWC5iVS5OS19YdDBOSSw/I246QyNCY2Vtb0A1QlQwZ3I6YjJNKl4xWjFyZEdvOl4sZj9ucEdvQSwKUSZKUEZiLilgZVdXMmw8QjxALDhoRSg0PGVcUCtDTGFAZUkwOXE5NllxZE09UVM2cyk4ZVpPTU9dYDhkQzE3J2RLb2JPW2JMVVB1QEJIRHQKbENrMUU/KSVbMStwW084NDVLXk0qamZpRD00RCItWD5IYF42MyZFWydKaCFdYCQ5YzZOSS9PNWhjdD1wZXFpaFFkQ0haPkBUalhjXztJZzAKVDNGWDRZUWxvZ1lfPCRvKDt0RkpMNT5hXGpGM1hDOSpRPF1DPTQlcVEmUjQmbCIvLDglPFpkXClpTmhOYiMxU1tqNGEkdVMjYjBWK0NyP0QKUlcwaG4icTxaNU83JzctZmcxOkZcczgjJ1AobyVMRGpFYDpWYz51MU5mXzB1U0xSI2csV1s+KlJncyNBJTshZ108WnMhI1dYLzZmZlFcO2cKYFRoLWQ0a1JkSEdKMmBVR2JjOT8lQCdxUCNxKjM+LktQOm05amtWI2IkXzpKcExQZHFubGpsXzIpOzlqMWxITTcwWGNZS0pTJzBWYCcsKDoKa3VJKTs9Ol9gUzQ9I1EzcWtdJWskLU1rZWQrQiNwYU5fczA6ZCRSYllebj1BWHQjKF4kNjomcCFLODViKzsyTitfTzFrXEw6ZUpgXiJXUUYKVTVYU0lpckU7PW9SMko8PmUmMCtLM1FFLy44VkRWMGdGKlJpOSRePiljImQiX05sLVU6SCRHOFMtIzRjK3FeNytwNCs2L1RjdW48bDVqVGcKJ01NPnVlNWVyWlJSOTFbVzNIc3RVSFYpZFYyZm9dZ1pOQVFOb2RIXVVHRGpRNmkoP3A6WSIsISdHalQ2SFdZIVZMZjsnK3AnMFVCMU89I1EKXDg+RlpdYnNuYzdbMWxlPV5nYEspZTxZdCliMjchUyJPWGJVSlxfV09FPF89JHBcLS1cOnIyLGBsbXEvNDQnQUtAa3VoKSpJVFgxJ18wIjcKNlIzUjdaODA/XD9BPjFqInNlJis4cVg5YCtobF1mIkxCITRfPCsnT3JxMjxoay1RLmE1ZyY0aTVYTEptaDw2XU4uL0w2LEQicUQ9VHBXXHQKQ20xUyYtPTopVipuOG1dPDpDazE5ME1obmVacjFEVlpDa0JUdCZDOC1PRjQ+LnFcKStiMDgiMzxlN1tIJEE3JVpsQ18xc2RGRCInZVBIYF4KUGBgc0ZGVi9tLyVjTzolQSpzcl43ZC4/K0BtdW4oSzAtY3FPQkUtJ180J2UjbnR1c2VZO1I/SkwkJltmaF0sX1oiP148KyxRdU4+KXEzWUAKZXFjbTciLylxKFpuLVghVGQqLGk0Y0AwcVg6PG90T1MtdVQuVFRgOChXUy8rPSlyR1BmRkk5STFESVNgbEQ+RUc4MT45cTZxKSRZL0VAYD8KRyNLcj03JiQmSkhPVzkqbDModEYnVSQ4ay8iVHVwJlNlTl9KZ0pNTDFzMmpBIlJwPzdUcywuRm9IXUQuakdgX2AmZkZiTGJpKjBGMCokZHUKQmw6NWhlNTNIRmhdRS5ONlFZLVA9T1VcLW1Ec1liPGwnWyNFWEwoWjFgYjpjVzA0LEw0SWk0Yk1eSiw3XDo+PHImTnJtSDNrPyFEPlVHMCQKb1IySmJmMC9ZRDpSOTdXOE5sNVckSUFWP1YhRm4hP0A4L0NJOHRWVEhFa047MERrWUxHZC4rKn4+CmVuZHN0cmVhbQplbmRvYmoKMjMgMCBvYmoKPDwgL1R5cGUgL1hPYmplY3QKL1N1YnR5cGUgL0ltYWdlCi9XaWR0aCAxNjUKL0hlaWdodCAxNDAwCi9Db2xvclNwYWNlIC9EZXZpY2VHcmF5Ci9CaXRzUGVyQ29tcG9uZW50IDgKL0xlbmd0aCA0NjI1Ci9GaWx0ZXIgWy9BU0NJSTg1RGVjb2RlIC9GbGF0ZURlY29kZV0KPj5zdHJlYW0KR2IiL2tOSFVpKSVScCdpMD9tZDBfMThmT1onMkY8RXFhR2pWQDpDVU46ZFMjW0stQC56enp6enp6enp6enp6enp6enp6enp6enp6enp6enoKenp6enp6enp6enp6enp6enp6enp6enp6enp6enp6enp6enp6ITttInJyZ09QIkxmUyIuczhEYms6WFcjITo+UyJESl1NYlxhRFhvITQwcygKRWEldV0oYmZpdWxoYT1EPDo1WlRYMjRPIihPYm8ocFZDZidxUmtkRDIkOks/Jl1BXltYa3RtPSJaI09eVjQ9RkIpUWklM0xDV1BtWTtJMzAKXjRETEMiMGEjJ1Rqa2deaG9tdHNSUEY0I2NsNDIkUG9fXnRcKlttaG00PT4xNWktLjAyXiY9NllMXikhcEllRVtwXT8zTiJtbCctdURTaicKXEF1PCZZYSw6SWAwMylLazNgZ01NZE4zTTlLQ0tHNiFXSUtDRVpiJCk7SG4+UkokZygsQWA0PF9NUlduXUMjZlFWUjhsa0BhUVpVUj9mVmAKVmBhLlk2IzRqJD9NSjxCXUQ8ZCMhVjNBNTxhT3RDbDcwYDZPNzBfOk47cGRcSkUySCVjPzVFaiZyIzJhTT1ZKHEoN01nP2QsQGRvWlBESFIKTklET0ZTVCNGSSNQNiRbQT1jbHEsOkVbbD4lLGU9SG9ePFxtTWk9JiJUQjtAKkZhJi5vWmIvV2MvIldTJGpTO1U0OVUqLkI/cipWP2Q5K10KKGthOy5mZmNFXCMkWmFwOT4qMWFYVDJrOztaYW9pJ0IxMjEyVT4tQWMvP0JiN3A+MDZdSW9OM1gjTld0LUZcPl4mbiVTYUZwZ1FES1wuXUgKJUN1KkNVbVxOZ3E3PFhxb0ImMSlnVCY9R11cTmJdSU9uYSwqQXJrajwtLSZFbWlUIkVjYiJqKD44OEhTUXIjOGRQal5FdHBdcUE3YHBoKz8KKjpXLjIuNjFHXVslP11RLSdkRCxoS24jXG1AXjE9Jj46b2dzaXFQO18xQGM8QU0sJG4iMURvcmRBYTRhP18zRG07TjlPUD9jSm03RyRiSyUKbmlyZS0vYVoxL1pUIXFJWTE+OysqZj9Ac24vVCItLm5mX1k8b10xNDFyT19xJ1xGWD1tYzI2dFhXay8tbjpARjQxbEpYJk9cS0wicXJkViUKLUZqQFVPW2JJSUA4QTBLZ3AkZS44WkgsO2wiSyQ9NiwrSDk4XCZRXiE+NWIlNEVOO2sqWSMxMTdBJlAmWFwiPmI5cXV1MEpPUGZhOGMyIWMKa1tzSFA5Nzh1JEZudD5pPW1SYD1KNGJsLGZiYnVUMTUmW0RRQU1EQkM0IkBta1YxLEU8XCcuZEIwMzMuTzw4VkZgaD48PzYxVEEkLlBfSlgKVl4sKHN1LitYc2NCQERmR2QkOko+UXQqLHA+Xk5ZJD9ldSpEQmFoVSsjdUZQYUNna1M+W1czZSg2Xm43NTIkWUpKTiwmcSQ3JjUxY05RPGQKQig2TWQmbSUoQTdQUmdcOiczNDo7anFmSSUlJGY7JmdcRjk9bDJNQEVxTzpvPnJBbCcqai49MVszbDReay88QEduKiJDQ1NmMlxZaEd1TFoKK250a19DRGIzQE4mUCdWNSY0LmBxLTx1ZG1rN2ROW2ArLyxUVy09OFszO2loJGVUIkZ1N2l0VHRaZEtQMk9dPywjSEJZcWJIZyo8OUE8IyIKWDVBP1dxMilcS1VyVloqKEBIRDc8LF1dSyUwIkIkPGxec3E6V0hWNEs8OGsnUj9tTlJZYUBtZD5dTC0zSl08VGEqSFRzPG9ndExsZFJNU08KMURgU2MhXkQlKCdxbDlgUkgqW21yUjRGUSpwUjhgaFFSLVpbbXBdbHRMQEAsalE6PyJXYk0/QHJkRWg8MypraWxpO2EnUD09KEhDPSg9bGcKZmQkODhzW20tS0BvNSFmNzlXOG1jOmMsWHVROiova2FgbDM3NTYkYkRlJTswOlRrJDVoclVhIVRGa2JsWVVjWiEqUiFIQzNVTVpGVmhxbkwKO3FwUDJrOlBOInI0XSpIRVk+ZiFBV2VyQFEtXGs0QTdAJGNBVWNVOFdcUChDLEJNZ1w9RCchV1gsME1BOj10WmE2NE46WjFNLTxcY09AalkKPmxhSFA5PVgqXj4oR2tTcWEybCVuTXQuajpjaW8rPEJZKkZSVz4+TUI9U0svaU5sOktSN1BZcktPazEwX1lPXCJOMlhmL001W0gxLzdBX0wKaEt1WC8+bzlxR1JbbHJoaz1PJFdvOk9fXk4vU0IlXCxJKEgoVkMsZExRbjBjXHAzPVNvP2EuZWFXT1wxbSxuayFZJiJScHIxRjJPLSZOTC0KRkxFR2VMYHFjNjpvWil0LypGNUhOL2xFJV0zKDQwcy1XOVMnQ2InREZdQ2UpdGxEL1slampORUtCMS9VZ01MJEU5Q1pMOi4iJVR0STxbVWcKSnJPSnVKKF44NFxZK2xbTVgmX0A+YkxHMXJxaWM+aTA7KXJxTV9tSlcmVHVhLWFINixWJW42PnBadCZoWkVia0MyJlIiXDRDZCVAcDdUVWgKJDg9ZFBmcTBVNGNqPy03IyhzLDwjWSg0U1JtJi1UbEZZTW1mbTwnNGozbEJOZlsnIXRyYVJYWnMnXVk0WEgiLzBfYSJOZFQlSmlyXzpkXTEKTltSZSowUm43Lmc2UFEzQVNCOVQtbEdyLmJvUV5QMXFEMG51cD1SZS8jXkdXUVdxP1ZzP1xJOlYpJnVdT1QkKjhGWl0rU01sYENbZlpJQSgKPDRWIlspMCY2QUwnLEVJV1BZQy9fNnM+ajElQkUza2FfUCtdRkVkVWw9XHMmZ0hBMEgrbEtMcj5cX01QI2lRPWwpO3BDaD5tdHAyY1kuV3IKS1pJYGxUb2knOVBdaDQnOSZ1ZnRxbUdILyQ8S09XTi9YZSEvMkIoYm5NZEAtdTNgT29GKF4zWVllJCopJjJOY1BgcmcldCk+ZWo0bmRkMEgKSGxCWk1gLFpPZGdrLSRoJzE3YzZLbGBEXDQzXlo/OXA8W2VRRU9PYW0qJkdkXmloJFZtXEo3UF47RWsqXlI9UmYyUidAUFdSKGplQk5DN0oKNVtwXkhtQEJoJTRPLlljcjwwKzVsMCJhV0tmJkRtbztwZ2docFBmXXBhRHJNKzA+dDpxXkdMJkNiTkIxRlsxIjlrL0w1Jjw3L1JDRjpNQSIKKEZQIWwvTV0pQWglczNyVTwlODNvNCUsc1hoRl1lbD9ZI2JLM0s7WlNGU3BILWIrTSw+YXIuZkliZCNHVjxEMEEqT2trIikjKWEnUkNUM1oKWnB1UTY6SiplXCJjZ11LO1hoZyMuaTI0SmgsO04nXVtSRkArN09dVCdSMi9yVko5dT9GNj1lMFZbcSMuXzckZGc7WlNORHRTJ05FWkFfM0sKTDI1IyJLTHQ3Q2tDIyNnOVVlbUdVL0pSbiE9YW4tS0okYlgoZDQxNkMvTiguZVgnMDowSEpAY0JDMmt0ODIpP0k0YmBdJTlVRmQxJz9iVF4KQFElWjIiUm1PczFlLEhkJG4uPkVdUC8nQkU+Z0g4YWxxPWBoXzNASjgtPTVpQUxgKT5McC8lWyFFJVc9UlExYSQnLlMkVUZqR19bSDRLam8KIkBFdXJbZFRLXUtVJ3E/ZFNLVUZLZi1Nc3RRMFpZTTxXOVQ5RldPLVUzLU4nS2hDY1UyI0t1UzhdQlxiYm9IRXEoVVdMZ1NlSkJMWj1IcUMKLFNgUy1QZzQ7dTFaIkpLMVdWYDFqSFJOQU5ZTVk4QjZEP1hLU183dCQyWDZyVldQUy8kbS5WbmFKI2pBZkZLSWQrZEojazxybEJxLEgoMVcKWiNualxCKVcsTzZPLXJERWciMCknOlQuT3RdMzNqTj9zOUAiLGtqTnUvST8jWDNcNjV1LFkwPmg/J0gzcTJDM3A+MFpgb19jZGIuXywuXTcKMDkhb05KRVhcYmNSJ25lMj9LUEIkcEdPIz4sLW1rR2NbPCtFO0ppMStLNktoXUlpLFNOIUBvdGA0ZF46P0khU0NqVjY4M2smXWNoPj4xcCUKLmg8bEomaztbTVs0JSlbck9kXWknYWtnMyhbQCFRLCxBVjNtaFxMIUQqKFdTMUxgTEYhWjhcbUlgbkJQWChIOWdRUm5QJDwmTFUySGleIS4KUTQmIl8iIidKcUdnJmssMU9BKWQ5ZyNQJmNITUVsXzUpaWtxWk1TbylxajU+WS5wI1xtNjIuTVReVFh0Vk5DTyJIZVYyPU5nUzgicSdXR1MKczdBRm5KLS41dD0qWzc1OV1AaFQmPTs9SmUqLWxrb1kiMXVWPWpkWzJIYG9RXmRgSVQ0VVBsTGNURmVKXTVMblRhPzJLdU1BW2hCaG00PjIKIVwrUixjNztldF9BQi9JckhsLGZFOUpdZyE7RlNTRUU4TWA0Wi1DQTFabioxUVpkRmZoOlBjTzlMTV9PVGZXbz9RMU5RQ0ROO2hIYU9eUTgKbGIkQzhTJ2xLR04+MSlyb10uPFZUZmInWG1cODc6YWdmLihGJ2dkTl0/PS9uQiNoP1BROUJqWnJFQylwR0JOQ29yKWxuclk8PlxXO1hMbFEKVVBSP1MlKktkZyZGcW85dTBLb3FVW29xLjsvNCFxRWVjKGgpVUVdT1lYaU83aCtrPVNwYkFhbz1FKGgnaSpVQ3BgYEtwcmYnK0RlSD0ndEIKXURMdWwqYUosOGY2KThNdVohRkUxU3NeW1k+aj9zO2NEcVY5ZThpSiYvNGZlOT9VR1BnP0tnZmlTNm0nJTQtQitWa1t0YWlIPmw6KjwzUHEKMjknU1hALk5tTjZGYzE3SE4kI1shZGglcm4+Z0pfYysncjYyRCUmbVtdQF8iRT9lRjRPUk0mY1NvSEtFcTdYXnU8TS08XGNPQGUsSkBDcGEKVCZfWDxDalZWal9TSTtccFVOKlo4SWVCRjc5aztzVUFUO1Q/LDB1J2dNSSU5YC1XI2ZWOUgnNXM6czFRZSs3WC1qbCFaU20ia0xZZTdWPjQKX2xcYE0zXjJxYEo6P1R1SDE0RC1jMTpPNU5RTU8oX11IUUpuPj9cZGkzbD9jLGRSbChPQnVcVEM3OTFYL0l0I1chaVFnOVBCWEliSzVcLlAKIlJrYztTXWwyWUxnYjsuaUsmdEdPbSdzNGBqYU4oZFBFR0VBKDhiaUUmX0Y5aFkwLzw+bl1IJllUQ2YuWT9EXU4jcSJpMz4rKlhHT0ZZIlcKK0hjS0VmYTYhPSFbaVM+P0lQSmRFajcyXCQ2XSZpXXJRMG1yajJnbUg/Rmk9X14lMEI5ZWFUTW1zNCRlRzJrMzEjPURjPyU6aUAvcykrJjEKQls7UTIpPDZMJi0sIm1wRS01XVQoZ05AXCkzLzVSNlNRcjtlOGgoJzZDMjpXP3FiO3AscyFvOVA3OlsqMXMva0U7TlE7O3NqUnBIKlpPJEsKdGonNTZZWS9BT284Ny89QFA/cnRgKipDZSNiVz5uS1VDU0BhR29vT0ZSOWJxUUc9RGpRMGczMSFbNCheNz4pcD5PXWlxZWw+Olc9LClxZSQKX0sjbVZANE0hKW80LShFVGVyQCFMVXMzU1prSiJMISpuJGZBZiU7U0szLU8pcE9TZXJabytGdE9tTlgoa0BPImJ1JTxwOEI7IVtcJ1JXdE8KPCtARCY4VjlmOidELEBqOTJqTzZMZXU5RjNwRDdOYkJAVUQ1MERVTXA+KWUoVU9pN3BBKUpibkssMjY4NC5cKylpOzpOKzRDY0ppYSc3Jz4KOXFGZkBjPiV1ZjpbNUkzOC9pIW9QRypKPlVMaEtSZC41KyYmPD9yNmdIUSRKKUBia0gwaU5PbFsqLWwqZSlCVDozZWYlSThFWWFSTUVoNVkKVzU7SHNLLmYnREFyals6Iy1RRkVhYkkvKyIuazFLa0FOWTkiXElHUSQlcD4+KS42NFUmZFFXKSk7SFwmMEcyTW5mRm9NIXVmP2slPDAnVSYKLWI9JChdYipZYiNfdTQpSj5FYmUvSFFycEg2c05kIWVtVzg9W2RKYG4sMWxdQjdBWjpSRmYzWTJxLENvOFQlVWhdOCJTVz96ISEhIlxPb0cKSUUvUVM1fj4KZW5kc3RyZWFtCmVuZG9iagoyNCAwIG9iago8PCAvVHlwZSAvWE9iamVjdAovU3VidHlwZSAvSW1hZ2UKL1dpZHRoIDgwMAovSGVpZ2h0IDE0MDAKL0NvbG9yU3BhY2UgL0RldmljZUdyYXkKL0JpdHNQZXJDb21wb25lbnQgOAovTGVuZ3RoIDI0MzExCi9GaWx0ZXIgWy9BU0NJSTg1RGVjb2RlIC9GbGF0ZURlY29kZV0KPj5zdHJlYW0KR2IiL2xtP1piQSVScCFnMD9tZEhNPyYzMGtMYnJdMiFrME4/b2o+N1JcYU5bX3ByaD8hPDwqInp6enp6enp6enp6enp6enp6enp6enp6enoKenp6enp6enp6enp6enp6enp6enp6enp6enp6enp6enp6eiEhISM3al1VRyJIVF9AMSFkdCVzbV5iWSluO1V0JnMyVSZdIjI5aDxGUysmZiEKZUc4N1QvRThQcGczTWROQlo5SVdSL1xrcSNwSTYkQ21SVS9wbztlRE4tXjhDTFIpRkJMO0c0VExqaGlya3NOK2k7OUlxY2AoOWYyYzxUM3IKUWJpSUo/UGBGNURWTV05LkBEWUpqLypTYW9EJj1xTEojPEkvbHQmU3RANTxKL3I7V19TPTVKUWQ4TTZyMG9cQl5xOFEsZEo9dEhKZmh1LmIKQk1CYXFac3IkI2xjNGxuam4kIl9gN0xDSWd0WUsyPlVFUSgnJStoJjpOQCZtXDE2ZTNibjxXXVlrM0k5YmNqUCxuMG5NTEgpQWFLZytHUF8KXkpwP3MrIklxbmJeNVJKSFhxQD5qNUFLTi5TK2hmNjwicGdWNiUrNTJeMFw4S1JyITZaOzgxWDk3QzRGQmxWTVw7JEAjYmFvNjkiMCU/TWgKYGonTlleUk9dImxLZSFlJlYlTTJgVWtjIVF1RmE7OzA3Y0plbjM1RG8nblg9VFVpMUUoLUBsYzAoOltqMC5hIlE/Zzo0bGJlcjBeYDREaHMKLzUxLnFLUT85TkZpX0pKN2ExXzUyXSs4XChvRnVZPEVSWEkvZVQvKS4ybkFcbzgsaVFlXitdPThKS1doUmJnUFpjRj91P2svO19oP0Q/OVkKbDBfZzh1ck8rIWYlRVJpdGhSZ2JDRjBxZmJmb21EPWRpVk9rNUJmImZjWmJOQlVgJkA8M1hLRnFEcjtCQGZFUUQiQHMwOUE+Kl49Ji8vPTUKOCNeND8nbjwxIjdfMW0xVlkjYzNvRVhTSFtxYU5WdWVZPDNVP0AjRS5JISlnOyolWitwKkMzUUlXPjZtQVNUVzRiRmBKLzFMPVs1NlxoLjkKNXFUMDBnSmomO10oRUgpQzMuJmpBLGUxNnFSOC9wXFxyZ1cobE1GVSg9MyNqU0REKjV1Q2FCNm0iKzVpT2tCSU9gdCIkIUwlSVxwWGxlWl0KSz1tKDdOWDtoWCowYC5tbEc2Z28nYEIxV1VFLzRRJ1o5IT9HT3UkUlNodDVcamxIYUMyLTduSl4hITwqLTBMKzskWl9sO2BkbCVpb1dZRicKWSRCT18vRSgqcjZLLD1rQWsmUCsmOVtkOjk7YltBJCNDNmQiZGE0Ii5YbkBPNitAKjU9X1kkRkUnbU1obnEhYE4qP0FZaS9wKC1vTzAvbEMKZWI5JUtBTVNPP205QiZGYXVbTCMybjxWQnIhcEQnQi4ra1wpRzNCcW0pMGFiUiRbXEpOaSJUYU4ucUBTOCVMQiNzTWxaQWZiPXFHRm9UJFgKKTo2bHEpKTo5LnBYWFo5b3BqLTVmLT1WXG1EdUxEV1UnNDhxSjszMXBzcSZvRHVrXlhxdDMlcVQoXi9mLTQmbGU8L0EnTFNfW1MkJk1MOUwKJUQ3bDdyMlZoXFsxayIxJ1piVXFKYCEoQWhfXiUtXGAhcystOGI9WU8nTVxXNDZATV5GN0FEb0RvOF9wUDJmYGhbSEp1IysnIiFnYU1XU18KKkVWKUNRLiJIZGRzPXNrL09WS09AZFdHaG8rLDpnSHNJQTZncD87TSNKQTo4LGRPbnJGL1xwa1FBITFKWDwubTYuPSxvX1t1bj86bVtzQkIKQnNwcG5vSz9HVyFyWUBIWmtbZ3A1UC9eX2ZhP0lgXUU5XyN0ZkxGSjc5TEhgNlsnNTVNOUtIUikjSCVEbUI3Nkk8WWtSKltNJG9TQVZZdEEKKDNOXm9KLDMzRVJaNUIhSiF1Rm1rcS1EVl8qUSgvVVxtSi82NydMW2ZNIiJOOnNiPlRsdFdnLl4rbm9EZ1EhKFchTTJua2Vqc0kncThCNisKKihdIkl1Wl5jUmhDLGRGK2ZrSXFBO0BaPjYiMGVrTkUjWmQ+akZiVy5POzU/aWtibDckYEgpSUA9LnVhdUYhdEo+M1hCTSJILiEqKGg6YEYKb3BSNj9MbVcnY05Ob0goZFhgJm8pNzEkVGByKVlmYmtAZnJfQSRSK049TllgMEgsJz9tWlk+aCQsJWZKN0EqR0xeLEpoaFl0VjxpMlJjc08KJ21qIW9Eb0c1PWUqMDBxSUVKR2BYRUIvQUFUa1YvKk9uIkxEQmAnXkMxWDdsVyNJWidXRCo7WVJkLCJyayYhLUFwRyFIPV9lIk4raCkmbl8KIVFDUlgrMHVzXlsxblpxTEhJVGVsRW1gNVRhci46KFBNaVY7JVxUVEZbRjIiVlRFbEhjKzpQJT1RInQ3Y0trPGNrODhqUDNrSV01TDNcZ0oKWTBsaypSYEdtPSE6bFBcOUw5S0w3ITJsanJzaExZalhjOF1BRVEwYDVmZG8/LiRBPzFgU1lHKzQqZ19EUDVEVzhEZyMlLVA0JzVkZVwyPFgKPk5ASys4USpBUXBaMm8wMCJOUSJwdC1WQHJcaTFIWCZAKCskLjRzb2cmUV5BXz5QMkdeRldhQCYhTyJRR0paNV9eUF8rW2xoO2JGJCs5UmwKalUsWS5nLHA/X1BqIWlvT0BdL0IxLitLIUYxdCU9T1labSoxbUFsUlJqWDVxUU04Kj5YU1NFVm5HIypGZTI3dU9kbGYtXl9oWSIrL0tLTl4KV09sVjUlJzMxSElbKVQvYGcqUyFLcDlQKDMsMlJpP3VgUysjMFk+XjhYNFJZZ0JBMW1jRD4+UEo5Omo5VGFNRGZfbWAyPjZbUXBMN2RULkkKSDQ3PGJQcTdlKF1eJydvcj1gVDZvMFh1P1FQc0VXJ3QncztWK0ZxIyooOURxa1ovODUpcHBZclcsQy1lQ2o+bW5IOik7JXNKQk9GLXFPST0KLy1MNm9tNT5vKyI3cyxKVWBZTSspZl1hOUYsOmchIV1xRzsyV2hyTyNUWnUrNCpPKmYiQGxuOEhcNUwtKzokQTZRXV02VjJvMzw4KWo1WkAKS0VaZk80NEYqSjptWzRqMkphITVHP2VwXkdTZ1ZKJS09Vlpxb1J1Q2FcXV9nIXNALyMscFVFKWRJckoyZ3E+MVwuazpZTFViMl9cLUReZyoKYGt1U2EoWiROWFVlUSZwYjJZTWlbTjRaRk9SOk9gZk5YZWpXXXVkQkZlNFsuVXNcciEtIWJsWmdGMzhuUHQjQiI5Tz8nTmg8b1o8P0NjNT0KMlBMR08lOWMtI0xvSnNGNW0hPClXVVZsIVUyRTkzSFFIZ0dQUl5BY14tMDtIXW5ELF40Sikoa0spUE5HZUJUW1VjOStNIUNLRHUyVksrbm8KK0cqOUpKVkRnXDExTydjWTM/VG9cbF0tIS9LUmgvclshUms0Ti9ZMy9uMDgtSWI7MipjLTZtY1wpIi1lLDo9YEVLJE1WUGVZOilAIj9lPDsKciwmTV5YTiNiZjVQZjZXJ25zIylNTEBfSkImdDtOL0BTYWRCWzZLcmktKm86RVdxMiEjUXBqOVEhVT1zIz5pLiVCL01bTU5jWUk9cClQRzcKVkgtXzdpWWpnKD0zPDlpaGpGK0c8SzUiMy0kbkcyOTpGLzkxInRwQSFNIXI3LW8jTzUrV1FpVShPQV1XKmRAaFUjMmZxXi1FZ2FXWGk3J2IKdFdhUUAjVjxPVSYwVSpmMz1ebDtNKjBhUXIyZC4oUV1kJGg3RUJtck5cNXAkZlA6QD04WG9VRj5HbERSJmxAXzIkITlCNl9nSCljajBLVEIKXSUtSShYJVFhUi1GSDQrPkFJaXE4NDMpR2hePHNRYXFUdWVvaHBgKCcrJV5IbyU8NDNzK3QoYVZpRzFQYlJoSjsqLEReTWZaZClaKldjLGMKQU9hQSoyW3A1ITwtOWRHUEEzOiwnc0NRL0hDMG1VRWMkQ0NPTzpaR1poXkFRW2EoOSJLTF9VbWErOUMxJFEnJ11SUWQhPjYqPG5hVz11NC4KMzlbXTJQN2dJIjJQdFc0QS5VZEIhKS9fXmA2bSw8Jjk2NSMmISMsTCY0Z0AiK0AtM1crWk9PS19AW2UqOyIsZmQ2aUk+cEFKVEtBJnRmXysKaFc7OVhoX05hNzBPTVhFVjdeI0AtXSVecnF0Zk0tKjVFYTp1RTxLI0woMVdqJThRRD5SVGtVa1E9UmZFX1ByZTZebWd0KE9hKig9UVFPVGcKXFkqZFZxOjI5SCwuNjVxS1FvN210Mi8iI0pgP2VxVWlSR0FTZ1gsSVwmJHAhUi9LbT9NMWZPb3E7biVpOURiQU5FPFVpV3EiNkBPMlE6SHMKJ1QiYT8vKEdHcUxBVj8qKEwydCJvPEhSLW07RGhmYzlMNm0ua2omYEAkUEhUKENHQ0NEN21XSXJXVDxxa3EwVXFdLV0+JmMuJHFuQTZSJWMKdSU5J2NPKighIz5nOFRPOEIlSlFMOWh1NS8rRUApVS9jPWNhKWMoWU9WTVhhPEBZYVRRLmJEXk9kJmA8P0l1dWFWRkksUXBEaEBjXkhlUl0KQT5jRyVzLmg+MyNCP2lEKV10YnJGZzQsUilcX0lJMTNncV5RJGciRzQmcjVcUlNzNUdmWlkmXiNcM1gyczo/dUU6aVxzWXJhJ0dmZyJVQFwKVVg5UUEjJGFHUHQ2PF9rc1tBcjI6Zy5KSTg6cGoiTkY4ZG08bmJJPFBZVlxwISJxYT9ibSRBYF1HbjY/RGc6OEUnNGlxLkZaIzshZCE7WCkKP0FucmQsKF9tQ0BoTy5fbjk6aikoOkRbLTVPdS9MKyJKLVtTOkdJREt1Uzs1VjZTWjFnUmlHQ1g9PEUsMWQwKFpORiFQJV9eTmszI0QmX0QKR1ZLRysjJEc6XWY4RGdZQGBSXiFBTWEoND5sZCUmTFlxYD8xXDA8MUFRKylcSFwxSW0hXGdMWkBwZzAhKE9KUzUtZnNpLEtEMzknIzkhJTUKcCpRS00yNjUvciM+b0MqRzImK1IkUW5yMEQtaDQ2TVpVXF5faz1RTmpUISNLLDpCR2tWbDA4ZztMVidlcV9fZUYtai5BamEiWDErcjZBPWkKLjxzW0lIW2ZkbkF1TDg/ZiVFViIlI0AhRkAlJychTjNVUz8tZmIlSEZDS01qOCUjN1otP3FGIjNQVnBJak9ySWskS0pCVktgdCIvV3A1PjMKajo0YGZyPUdNbFBcTnBRUiFndCozNUl0KCYxYm5BKG9OM1RvWl5CLT5YaDkzMT5IJEdtT1ZeLzVPI10yPE9FRCE2Y10/Ty82RkpbQnBbRzMKOTBUNCUnW0MsMnEmYFhBaFpzZkFvTVNlX101UzhSRG1kUFQpc0opIiEtUiNqb2tPMkVATXNLWzthRmUhS2EzRypwPF4/UTp1VWVTUk5LOkoKKGsjRTZLMzBFNGJMQkpDZyVhbV06XTVQQCVLUVBwb0Y1Iy4uNU0wQFhuSGhIJnFcQDtRIy9GJXRGOTkiKydJWz02REYnQ2pqNWQhVyU8aXIKNjlYTCEpJGFFMEIxJTkiIiNnJisrWFNzKzclOzlVV2svTy9MZiJkWkxeYHI1WWo2Lk4uVzNhbWNncj48c0IjbzVvaFkxSnJOaykqX1A1XUEKV2BuazhtJFIlMD4iV2xia18zMUNIdT45JUw4cUVXUjZKMlRBST5pInNnJzIqKU9tLG1NWWFRNCssMlgoa2kiXDUhVj8oNTQwSCZLJlwjKEsKZEMqYSFjWUY6blc0IlBKRFsoaypzcVxmaGptMVhEdXB1Xk1IaGdJIlFlaGw9VFUibT9pWUx1cCIxdCw4YT0yRyE2WUQiNCJgQTpUcExhJmcKNFBtdV4qSikoLFAyOlJOIzVlRnIjaCZcWmUwTUA4akc4bTtfVENZImhLNTVDM0ouOSJqYUQtLl8vKzIhRnM9XzNcOSNUcDVpWSUvQmRQS3IKOyNgWUxEYUVaNTJiPTA9bk9ON0BZWjVKM145N04iU2JGYm1ySFlVckBYVzNDVEQxWiM5NlYxUEQmbXFYNj0mIUE+MCY0MFM4WCUpQV5OdW0KXCdNX2A/Rz47YUxbdEZOX0sibzUyW10sWTMtZjVcaWpwSjVUX1pePFBEYlc/UUw0b05qQVI/SW0qTyc/clBZV2NrI2FsYkk7WmFQPVNONyIKXWxoQDcpMXIha1RoXzo1MCNYbERzLUImWD9PVm8rOTclQ1AqLFhJK2dIZGhRaER0ZS5GODxBLDk5OWxnJWlvW3JXS08oNVRlP0k7Qk8wYUQKXDRqbVUpRDtfRkU4JUJVakxrNSJUIVgnPDtINSlPJkc4aTc7LHQ8VVY3LTdCWkRVVGg9JTVcaFw9VURdc3Q/aSFlKFNZXigiPzVkPDlKVjAKMm05M2VRSyhJTyNsMyRDQGtzLWNkVl5qcGJ0SlxFKjs+VDxvIyZJb0hCUU41YDVmW09qYj5rbCtlOCRMTEZXZyJpMCZYaFIwQj1hb2sxNkIKQC10LitsdF0sKXBwJ0glPTRjSjIxQCl0YTk7RGgjTygzRixWbHRtTWBiQzNlUks1YGooOCNLM1tfTGgwdGJjZy4zM1wyO0o5TShyRVhwY2AKNlEpdXFULE1JSklcZ1lES0EnRU50UytPYFAxKmNqV0VbTEtESD4sXz0oWmplWlpVbVIkXjNsNSUianFEZjUxIio3MCdPYkcxam9NYSFkUTQKIzBaVzQoQCkpbyhNaW08WldPb0s6ZW8wKzZoTF1Ib0w8PmE5MVJ1bVdLU1ZLXSQhNyJwN1twQic4aVcyWzAmLk05TlpmaVRfWD4rS3BQMmMKcS50Ulk8IWBRIXFMXjUhLj8rVF0xPSNaSEstaFJnKHEsKDUwVj8zbl9oJSRxK1dvWVohKjdPKkFJKmQnOjlgbiNmaj1GT2gwUVMwTV5BUDsKbUZUdTZNI0ovKy80TiQwTV51SV9XQDxiPTBcVEshMGtpYyxzZCVeTjhSND0wMUl1LU43K1g5Y3FXVXVrUT9SJC5CQi1RaiozN09rbVNrPDUKb3RGZklbWWwjOk5nUTxeSUhiJlVnSG1BJUZEb1svP3VWLzAubzg/KjsqViQtXChnOFNuIkwxVSg8WC8hVyZiIVpBQSxtW3RjQ1BpKWcwWj0KQjlqVzk/OChgMFM5RmZSZFFbRjJtKXEvbTU2Iigob0FSbFInLiVOKyxvKz48LlI/MjUtNElNa2hUZ2xsL09bKV4kczR0cT9YRT9iPEpAUi0KTTVdaiJGclpDb1xtZXNicVk1Qm9FNi1ZOmxKPXVXa0RabCdTcmRYZyhNbk4hLWYtYSg3YDdDRm8tQFpBU0Anc3FnMG5jYzRCLE09blNLJDQKMFlBcmhqVUY6OEt1YzBWQkVPJ0lNVWFDWWpGN2gjdWMqTS4xOWVrYFtFNUVrckUpNEM5PjtrZCR1OGJKMmdyKDZoRE9iS0RHP1RZNi1VQDUKVGVgKWZfW0xDTStnU1ROOSduZUBCUUdmQlUqJWVHVDdXUTBrPihZTzxwaCtdI15yZko0PzFSVm5GZzUkJChSaUonW2w6I1A7SzYoOzlkbyYKI2VWcCovYHFlVlZqO1hBVTwvUG43XU9fb3JDQGg3aVtXK25uanA6TnNtYDhZcylyYCQpXiQuMFoqRWpYbEozckg+WV04WSFodU5eTSwkXCsKUE0+SFQoOXJeT2sjckJbOStKI0QhPC90NiVGLzs5c28nN2lUXUwwNkRZMXEhZC89NEpOYSZsUi8yJl5yZk5VblJbKzddKyhmblM1K21wcWUKJkRdP09KIlE6WSVESGVHcD9MRmUtPzljUGsqZ3RAPGo/WFJeST8wJzZzQWBoOjltbScxYFtCUGMlJC5icyorRFlSNWtiLFFacyxMTj1WZVAKZyI6Kjw6QzpuJzwvMUsyTkFXUWpWV0NrRXNNPTxCK15BZipbP1hBNEohYTZALlswRCoqLXBVYDRaZ2pOXjJNWkBjLE1oSTxyUG9cWSw1PC4KKTY+OWNGNk9WM15IbjFOLkhdTVFpOGplKj5IZDprYkghUlBzMU1hMmsoZ1dcVkBKOFgjMG5ERVNrZE1PYnFTQ2Q9SGZJWkZAdUBHS1ZpRGwKbShQNiZcPyZxOlBPRFZeTzMxLGwudUEtKEMuKjddSWg9Uj9KVSRNIWpMOjshPU9ZIWNqLExROkNrb0ZxaVIvOkEvJ2JeP2tVWGluLkwla0YKcEA/Jz8zcUt0XnFuZ14xLzlMTkV0Z0glZz45TVpSJ2QubjhaXElBNGJqTj5QX0hvYipJNV5gITVzaENZQDFYaTpXRThcS3JIcDBvV2wrYUQKXVdzTy4xNi1gKjJTXy9IUzdKPCEtMm8ybyMqKz4wMSJXUSQ6I3RTbDBAQTtMOS0zOlhgWFJNSUMqcTcqLU1cK1FWalVqYTdGQWRsLjJiRVgKb1d0WzktNnBtK3JpODo1aVttSjojRCxVWG9YITIxNEBycHFVM2hacEJtUDRTTT9nX1o+NHVfVUpgMkNbUjpOM2g+bm5hW1ZqMytKOk5PZmIKbCxVRyhvMlJZYy87LTZbM0k1cSVyZ2RfTEdKTXJGbW5TbFFkPThCVDU9dSciaXIsdDtaSnQqQDkmZzldQyk8JjVCN2krXC1ASSxndDNxcGkKSXQmQF0sLC9gISgvXmVCO1hQbUlKYzZsNihrSmBKMWU0cFYuWSVzMWktVCssUlQmR2JjVVtAZjFTJ2tRQSopRiJIKWFBQENUVyVbYTFpZ04Kcm9sPzVWNmE+ZXVmZzdwQU8nNyZyY0tsUSNFLk1bKzU1XUxWLmpUb3MjIT9wPE45RE0qQmlDRWlzJGFhSEtzVl5fIyxMTEtSI1lUNCVgOz0KTT0lSW9hXU4tKiZNNDVuP10ySydsRDM5bGkpOC5KRzpUNkswI1lIQSo5LDxpdXMtUHEoVW1tME03MlIzM2dlZF4zUERpISg6KS4yNlg0Z0oKNmY+OVM2JmVwcihBUmpcTWZAOWM6aTcpZy4ya0shRjc3VzUiO105Q145MSdYSkpTb0k2XD0kXHBjJjU8YGIpXzk9SjpIbkksJXBqUUZtRGUKSzElbV9OPy5AVVpWSEY0dCdkUyY0YyJxRS9DaVY6NDgpKSFJZy41b080akZbZFhSXXApXCZnPlFfYl9xWSQtaExjWzpvNllJVl9KM0k/Yl8KPV4wOkNDVzdISE80VXRMJ0lfLiZyLSs/NSpHZlAyb1laXGVjOjU4V2NJOS4mLSlOWl8jJDJnVyk0U1YyI3MvYWlxUitXJChzRzRbMEQuKUUKWlZYMTl1YHFRUkdwVV5PTDU6Q0c6XGZzRDdXME0uQzVMNzVqLzwpMEtnYHJRWyQ1aTAvLG5bYCtvbmY/dFRvS0RmZzZKVydwUlkuV25MWyYKI2NEUEBsIW46L05pbG5GI285Mik/QVhhb2VARnFddFA1aTJuVjQpVUNEQ1sjKFVuLWE/T1BdaFtJTHVlOyhGOUo5Uyo/R0lENGleKV46SWIKUT4iYFR1N3JfOyQkYU06b2ZZTTRmOyZKcWJzOStoVVtKXStdJkVmXyI9YCVXMTdXYW5kKS1IPTlDZ0VsITlLOCZCQk8rMU5pSGo7a00yYiEKM1hIKWtbW2loSkleVjE2XSs3dHJsdFhkSTJDaEFrYFs3KXFvZXNEXmY+Qy5MSXByKCs3UXUpXWMyQzg8UklwX2ErRWV1VUhGZ2thNVtNRzAKSHVpL18sIyopNSVLOE5BSDIrTCVzYGdAViprTyQqMiFoNVRFT1pQamMvc1A2QVxQYEs3OW8mOW5ucFhwTk9LUGpvVHBZYTtfcVkiOFhGLkwKVjNPZFEhUnVITGQjNCYmcT5jMkFOP3J1Xk9EIz8mPUdqLDBaNCJrO1E1XipLOEg0cSYiKEsiIkBYMV43aDJeJytAUkpFT1haO0c+Yls5cVIKS15HdExaZSNhRURoMkVCakA2NTJQNitrXykyNVpPdEwvKDk0NkoqRTw6LEReXWBwYkM5RD9wWlhOIlduP1dzbHAjaGBoVFpZInNxUVJYbUYKOUdsL1QhYGYlUVhvbzhTb1lyTSFKPWF0bjE9PXI7RjJKckxfOWc6Iz5FIjZYXWtRLFdfW0BaR3NpTVBlRGVdJyEzWzZJPFNAPjpgTWBHYFQKLC1LJTh1J0MlJlp1LkJPJFw2dDtmIldmVkdMb21yW0RAXyJebGs0Um5NJC1QTEc6YzRKNG4/MVQ3Z29VcS5AZkk6VXEwMFpPJF1wREE7WU4KMWVwVGRzIyxENyZeNE4iIiVIY3MnIm02JSk5XFRAX2gzXyEhLFErb2xdSz1iPGQ6cGt0KG5JR288ZXVmKVk7az1oXDFWTkw/RixTKjB0OmkKM2tSTjdLV0pyPz5FdS9fV0woIkJYIUJxIS05aGVWXHNuIl1mUnIoWTdIOWlAc0ZqIUUwTFhxQV5GZVhPKFNrXFtLXzdmPDhPWTgvIlFNRHEKZjIwWCNKQTg3XC1ASUFfSy9idUQhQWpcaDQnVSFtbDA+Pzd1P2FTITYyUk1VVjRGIyUtSCk2aiwlI2wwZCpxMEozQC5lQmgnaFpEbWs6amoKN0cnTDlgVFgvXVlBXiozQitDV19BXz5PV1I9OyhKQWcsZiFKV2U4WTV1QSMxNGNDWDtKYChxaGZ1VXRGSG1iYV5dPCpdQS1JMnVlOi1JM0AKZkw7R0o+TWw/Y3FmR185bG1RUGUhcHRnIy10cD47Z2spQ2RYYUYiZTV1KG1rUUNmZS5VITMoWyZNRS1NIjFCXj5RP28kW0dGPCdUM2p1YSoKc2AiKDBfSFVYPSwjNnJVaiksKkhCSHI4XWkrcHRpWDNXV2otcFMtcUBGPidWWj5BZUY8JGooY2graltJOCFpSXIkMyNbMjtMJ2w6XVVbTkEKKGdbZCpIOGcmRDxEdUBhJjVicT8oOyUpPjAlZSsjUlJNNkk1MmFrMjAsKTBsVyhpXEx0MlBbY0ZzJTlQIjMtOkBkLCwtaDZydWcydW1rNnEKPiNOQ1EmOlFrJVxwPFBiWSpAcGYpUyRsO25rcE9pMDNoUmNsMWk/T111cVEyIztgUFJpVE9QaEZMYURxSVxIXmQwSl9MZXFgdC8tMlgmJ2oKdSZUNyI4ayVqSzplVWxDbHNRTyI3WGUjX0MqN3RsKDMwUzI4SHNySVYxIT5KYV5SV21fNCxRKC9ETz9KT0pIQk1MUDRoMkhXa0Q1Si5kPVkKYjJ1OCYqcm1iNT4vZnJQakI6Z0w8L1c+bCdmSFkmMmtvayRdTjFiITo2SC9NdFxtQmYmcD4hJDNhdEIhRlFFY14iQGAjJXIjViwpbEdldSsKKTJrdGZLZ1NpMUFkKjtsXCJnQUpqKlFaLVtcaGJoXztaazNFYCduRFc5YWdEW2NcLixnMCtGW0pSXVk0XmVBbCo/OG82Pkc4OytKMCIzQCwKR0xzMjtvXmJtYFdCdClMXVQ/UCM3UWFLUkshW2hib1lZLyxfJC9PIzBPLSZtcC89J1k5KmIiK18uIW5sb01jV2tIY1JlK11kMjJwImBTYFAKPXJaL2tRPVN0JChtQkNJbS5eNyspaGh0VV9lPSJrUT1TU2k6PXE2W3VWdFkocTQlXTtaUTFTXDJsQXUzQEppUTxQTGpCMFQhRCw8O0kxQUoKRkRbSVxHJUhUWjo5QC5AJCY+ZipAdUlhbXU+cEE9dEhaWis8UyhiIUFtcVtJPmlxV2BnXlFGMT1lckZgaFVFXSIiX2plPkxTMF05PidyYC4KOV9aJSg2OlhlI2JzTC5nZSlxS25nRDpeSyNBai9eWS9iSE43TEtbb3RBKmlfQyZvKEJvR0clMEcxOkZlWjZebztHYzghUGVjdTddcVMxJEYKQz09bXJRRk41NTJWbC5JNzU1MyZVIXVaO0JPLjRuZVMpdSFQYUpeP0hnJzNLZytHIyNkNmhhQStTLVFLZlxabCpgLmZGWzshWzAyZUFscGsKUT9CJC9NYjppcnFYazJdS1Y0YF9BXz9qMF9NXjs/Tj4qPUVFSGgnNSttc2dsSTU/bmIqWUFRYkZRYyRUb1lyJTssU00rby9yOEBAIzFoRzoKZkFHWzNkWSY/ViQmIT81VFZVRTc3QVZgWUUlIThrNiJET15uRmZMJSw6OiYpS0loTDVXS1I+Rl1aV1FwMEZBUV8mISxdV2EnPiFaOCwoP1cKWGtnV2tpKzBRKHF0QihaJys2SzxKcitlSlBIXiVMR3FaX11YQSFTJm9TYVQoSiFcYStTXXFWTl4wOCduOD5GUEgxNj90Z0hHZWcjRiNrNF4KU1IhaERwRFVgKVkhXzBeZCdyQCctL0JzYTwqKlJxTExpJ05ycEJCT0ojaUdbWVRHdEAjMDsiKzhJKiJvZGY3Ry8yJCRzZm1OMnJHbFAnKisKTVBOdSokQ0dwOSkoVUpnMiNvJ1xwZTVAXCRTPXM4Sjo8PUlhJClZPCRGczNWS0tcTW9gPEYjJzo7PkpiVik6KTBdPG9NOjtZWXE5NGoia1IKKEVkUyNAKjJwSUUyaTo+Nk49ZT5gSmw2YTotJ28kMWBWSVRqclMjW2oxZGJpL3Mrbl9jXFBYaGplIS9CR3NfRGFFZmZBSj9vTURZPV1tSmgKTWpZVUdgblphRj4pLnVZKDVYVDs9TD5TR0ZWb20/X2UyNjBHcmRMYHNGTWdvbSxMcU5CXW9MMFMmK0FDJVdrWGYoajNOIXMqTihiK2gzRlIKNzArL08tKFJfJ1w8NWBmP1ltXTBccSpOTCtySVMxJlIiKTciI2NOIUovI3RdKS09UyUsVDhqQjNsWy8iVXM9Q2FhNXVDSjpBSV5wNCYkYzIKWUozMllJXVk5WDh1XVEuUTdpcTdvZkNeMiQvZEMzLzsoZ2hMTHEucTswImMrJ3M8TEdUUWwhIS0+QztkQ0EuXkNIVTpfWGBvOWZbL0MoLFMKO0dVSjpmbEhrX3VeN1xUVF47NzVwLFRlPDliXj5Jb3NLMUIjKmZtV11YZk1GLEsnJl9kRVZmIidoKURucV9kSktfZD4hMFpKJDVUYVVXdSUKIjAsLCplNWJsNTJgLz8sLTQ5VkUoUSlSPyM0K2c1JiJBND1VK3BIYy1wLzkhalFbQVtxMkwrOSlCLEdFSz09VWdQKCJTZFFONHUrZko1OmcKSz11Nl1YQDVAJjlSRSc3bVw7bFxYaWNmVkEvNHJgZCtBbm1HIypzczA8WFsxcTBbSlMrPSRqIS1VJjZWc2pULWswS2diW08kdF9gZDlTbEgKbjlRN0BIMDkoI0RsKk86NEFjZl40bkIhM2RYVUYtY15LJGNhJWxRXj5nJ0JKIWlMbW9vXCchPUxYQz4jRTNacy45U1xAJGpfN3U5NWtXIzUKMl1mLkNqaFtMIj1GYnI2dENBbkNAQnQxWzpYW1wxOWVqSzk1KmdLKGlFb0xWYF1BMSgqM1M8az4/WDdGaGU5Rm8vYWtEUnJaMURbb0Qva2QKTDJGMS1pZzBaQ1duTC9rMCNNaFYqNUEzb0R0ZUEqYkckTWpWNXVpaVlzQDYrLS5TXztaUS8taVsvL0dKTmZDTUdyKzttS24oLXNkKT8lXk8KPF4oXEw1TGdNNG47T2VtdWM0V2o4TyVZVEM4SE5XP1dzZlY1Smo5NnEmQ0RoZmQhZnBKOipCQWAoXGI1RkVvY3BkTWomMnAtUkEjZV5HP0kKaWJmUHJDY1NuIS4zQTFzM3JvTnFRUCIyXydvQDpNbjVUW1ArXDBEIiNxNExIZEVwQDhITS1SKi8lWkdsJ2xcXV8uckNMLGZdZDUoclVOZEAKR2I+XF1uYTQ+QGdPT2A5IUtEWy10J0dpUz1DUCU7XWxUIWszYy1aVCFgQkspam0xJS8lYzVBL0puSmw4KkRwPHFBQUtcL0NrTEpvRHMjaCoKUVBddEpqLys+JmMsVydeVj5obFttTUppI0VTR0MmTEw3VyJGTUElQHQiMSU9PERNYTE+T2hWTWVOJiNhOTBIJ2xRYyxKRlwnWTJjJCtlI2MKaW9AP2VaWlIuOUtqS0JbSysoYXI0cDMoRDNXKkFjPShsdW1eKyRoODtkcmhuZGgsKWghRGxAME5KbTxcVWFxJzRIYm5WY1NFKkthc2k+PDcKNTBHJ0w1MSRXIWJGTFVASi81Ql1SLTJdcXVWYlhPMylUTmk3aUpqQXA2Q1JILXNcKFQwcSlPZXFaJGVJNl0vSCs3I082PElqMGw3XTNKck0KUk9aMytPP05AVllPOSRfZTo9UHBDWUlLbE0vXnJlNzZmP0ItUzNvJ2BfdUlFIUZNLz9iLlNSRzglVDg6NUZabFRVc2ZKP1QuO11GUD0pNXIKNC5sWWlwZGs+NVVqa0tadGppRCNUIig0Rz45NiIpJDFxckZoTkczWDFmTy04KEptJXIhN145NT5Hazkwc15WNkFuX15hYFFgNmUrMU9NITsKXXVbTFwwTU8pcV4oXTxHI2c6WDQkXThKXkluQCNESms3Ji8oPVVLXDplTHMlTmFpL0k3LDRncGk7PlVHNUlCUi07Z0NGKCxaKSIja3BnLlQKQGA+UEl1XEhBJiIldSFJJSduNjxkZnFYWTVrXnJOQldaSXI5TDhDI0NJN21BPjYsI1k5TyheIzRxcC1Ac0w2NkowMVVfKyxMVTFedTg2LTQKcj0kRWY1L2NgNTJgSG5xQE1waCMsOjovcU9nLmA3aF0zR3FLakY0UWdzKzZtZ1JaNEp0TSZCbW5aNS5jYD08W2AkRVtvTnEndEBAP0hQLU4KNm1SU2dKKS1CaT5OJVNOVytlQV5gSCMwNl8tSUlwb0loVSMnXztmJWtpOGhodW80OSVpQWMkRCFuNGRVXFQ0QyFvSVU8KE9cT2JcaVRgZlYKKGowKlR0KEErKzBuIk9JdGcoWShLbkQhckZlQjU9Mj1BNlA+SWomWlVjL2xXPVs/XGosTlVEUzQoIkVWbnNoMUptUC1FXlhGJi9eXi1xM2cKVTxkLCZGMVI1aVBUU3NGVzs4XykmRClEbHFKW2FUWFsrPC9tc1gsZFY3LkZxajZGWjY2QmpLK1E0N2dmaVlTI01jMWhDQWApXFJMOkRvczMKIWp1OEk7dCljJ0UxLUlpXSZ1TTJyXmFGNUY+WHRyJE1Hc2hhaS80ckJdXk0wUzRCYFxNX15yOkVNayhVQUtTbUdAPy5uM0RSUUROP3JbYWIKPFddRWorJ0UzMFMkM2I+aylLUVMsYGpgRytmIWxKOSEsUzgqNC9EUlIkVFBWYklVYjloZkt0K0ZWVFlLVkw5KURXaEBDW1FZckIiOW9JW1wKKSpTJnFNN2FTTzQ2OWxJRUdjRWhrXEo3MTwkKG8+R1xKZGJqR2A0SjFmQC1WZiMvPiVuKD9xPGAxLVpxazRxa21kLzNhJiphRU9HVlBWWmUKPlhHRyJmVyRJNUY7XypiLHVnMSYxR05KTE5eQCFFKz0pRkwnSkZibkRQMWc+WWVZXWFOIjQmcEw3QUw3NlElbjBhZFMuQHNRQlhDcW9UUVIKLD5mOkRhXDNHLmVwOjI4NFtGSWNsMk5UQVMyIz5KOj08X1dvMGRiTVAxWk4rVjFccW9EdGAybU1lXl9aOipqKWNESHFpIT0kczU+T2hQQS8Kcy8uaDRoSEBVWWBHZkM+MSQ4Ml8wa0VHNGFfY2NYU2IyXk4mbjVZPmYobDo5V3RiPChRdHRcM2tZZC8jWlcnIUUsWDUsckRgRFJnKG91NVIKWXFgdDdwczAnIWFwW1NrWC4hWWIuUSssOU9rNGhNVEIhY0AkMHI8WD8xPEBrPDI5VmFVNTpdXldbRGknSl9BKVB1MVpiOTxTQUVeajFhP2gKJTVcNGg4OXVlVWZXYlFeZUNQLlJGMEUkS1UyN0pqOGcoWkZGPTpYIUReVWpEPm9BIShyLmQjcUZLW286MkU7ZyUwJWtqbkRkVWZTY1tpTTMKNmojXmA4MkpDTEFfLnIpdUoyWGNzW0ZIays2VFZZQ3NQMWIrVD81OHBiP2Y1Q1dWRikrclxnTUdLO0NMTDhFJyJQPXNPUmItX2xjY0Y+M20KQ0Q4O2JEYl1FR0IoJydsMW0hWWNkaT0mSlApYShoMkw5KUcjWE1kaSQ5OmotayJeVF0vIlkkKyloa0hvbGUmLCJxNDVwa1g0Mk0yTm9EOjoKKEdzR2UmZjQnITJiJk1qVlY0bm8pJz5wLDRFNTlGZDQ1MzpDWSd0cD4pZmc/WFskTl5fNVFJNz0wTzBfbHIhZj0nLFFQLkVUKyEmRHBhKVMKPlJ0QElaV1pwS3BbXyMuW0hfNz5gNi9zNVIlYCRSKHFKaShMUTJZLUxJIWRLMSxcYk82P1pNT1FCXGJKREFtUEhvJSIjWTAuLVU3bjduTmYKI0pxXHBNYmEqQCMwNC1ZS2YpN0pDIk5ATWtMWVkpQ2o5bS8yUm1ANW8kbTBHJCRuSl1KVDdPYnRFRjsxX1NARjwuLmpHJ0VFSXNQdUZxRmgKZDt1PUdyZ011cnRHSjFgKmxAbUkoSmUiYW8ncT5KKidOLWQsR1ViOU1aL0YjTCNKXFFMIywjRjNJQWhjai5jXEhDIW4uKUgxMTEiMDc5ODUKK2k5OnBlZGsyZmNOJyZFTFdwPD8vUGQvX3BzT2w6UCFfSixBUC0xTCRiNShNODslTlskWSw7OSdTZCNrQjVHOCQzPmxhckdIKGxvazdnQ2sKMyR1Pi4yNyRWaCg7UnUuak4lL211JihhOnMoKlg+VVJNRElWQjVrLi07Lk5NLCYnIT9OdC9RX04sJygqKGRuWkdKRSNKbFpxU3JDUTdOWyMKMEFqTVpLZ0shSnU9W21UL1ZNK0lpTCI7OjRZIz82IlkyYXJtKlRyTm5uX3BcYlorOCJSJS1IR0JhcCNycWQ9Jl5SQWs9OyRpK15eQW00OiwKaE82N0VhcmxDb2FxP2xsUzE0QTw7RFxvUHM+YWFkNzhzKlxuKEpSczV1TG1mT2lNOV1PTDY/N2QpVTUrNzBcREJSLCQ2VFM8WlRMVUF1SWsKN3VNcCpGQXNyMTpeJEwhVE9rXm1YQi5JJHUib25RSDQjcFFwWEdXLmltKGYhU2xCPURNMnRTST5BRkVJWmg6ZDJHSDhiUDU2U3A/JWQxZFAKTl9aOXI2UWFuJiQqXTQ+Q3VBNStsMzAyb0otQiVKcSZpR3FuJlx1WyktcCc2dD9obCItMUZPN3BcSFBKVGdtYShQdD82LGtTV1VTNzF1JmkKOCd1WmQwSDFuPEM1IV08ckYoZ0E6alkxSGtKajhwUS1caDRrVFZQVjtIIThTNl5pIS5oWTs9UWROa1BsLmtKLkZoZTYlakROXzZaJGxQYVoKaG5DQSNNXkV0Lkcvb2dCdW06cCJpOjRLSldXNFdtIitYNDddOy5BZGlKM1IqJmtOJGwyTEUpa1FBQ0VDZV5nLyZpdDRoSSNAY1FiOF4vNGkKVlUwRzUyblUlQiN1XD1JNlxWNVxwclMxUUNWc19GLzNsUXBGaEw8YTkrP11cJzs5UUMkMFZUYTpeYHRgSC9pYScmWWlDInIwVVE4ZThGXSgKK2MzUXI7QjpXIS5dSilndSMmO2tyTXFaNm9qOSJVXFBtWDVVVS0iQ0t0MidtQ1VFVlZ1Y01rcXJrKCJBSV1yYlpmLkJWSUhVYiVaTT1YdTUKTSJBRWIuMSE9T1xULGI4QlNRS0s9bmNNPSEsVSlVTUhmMS0wZyFrQTpNdElJKkpkO0RhOVJPZmAjOmRjSUY3UV82Li1WKD9tR1xGOk5nYTwKaER1a0kzJCdhVyM3VWEhIi00dFhzIj1eYEB1ayMhJV10YSZMQExraV1CZ3BJYVRTVHFMRkxfcW9iNUlbP0lLbWFPLCFUYDlrPURIZXBiYWcKSSxPaE5YKHE9N3QiSSE6ZkFYYk83alJXIihsI29HRW1sSDUxQyI8IypCLz9QMkNQRV1CLz5HO2dTJWJZIUw8X0p0Umwkb1YoKSpeJidsMDcKM0FiT1QxX2siTWkuIXE+S0V1azJabkZUYkdwVltoN2BQOT0xM1dFaS5fM3M2OFsnXDtuUlQvW2loRmpTYDUlQCJsUzAzWEkxOmo2VUNFUkIKLEJiNj8zcDQkRlJkNUkhL29cNXJDYlYiPUBOL2FOTFBzNGpUcDkqaF9dTlZmOGptRk9sXFNySj1rbllwclptW0dSJlwtQzJhWCciKlJlSHIKa0QkT14/JGZpZEFaPEFwYyRdI1tXMV5RV1JIWDlMMl9EWCZDPS5GZj1daXU4ZnAqQCtNV0Z1LFdvamZVR18+Tm8rMSRJTl1dQGclOkdDUkcKcUZHLGxYW2onbT9kZnBrYmpxKGQjVGUzMz5TXSlsP19HQEk0WFJhUlRSRFAvUSRxdThII2xsS0tgXismLiNyLixtUUBWcGNuOVFRWVtlYW0KRShcKm9EcFU5WUVvJGBkIlJTNW8na1NiJFJmRW5LS2MmXz1hVVlrTz47KyJqWDZKU0xQbkc/MFNGWF1fdCknailUOy9iO2JgXWNLIUtZLkkKRD81RWBNTkpObycsbV4mVU8xNTVjJmgqJTpvPXVTJ1RJY0UuI0hSXCkoRWlERllPcDlsOW9BRGNhWDkkW29mNDgqVCVXTyo2P1EzXG4jTW8KdDM6M3FdPmlmTz42am02am09RTBDZnM4KjplVjQkTUMpUi1qQSglQ0hxbz4yMy0nU3IyT2EwIzJrXHNnZlZTcWxkTF4pV006ajUhPFg8PTQKMVtZV0NGJj0ySDJaQkZOXXMsKlx1VTtjR1s9PSZgci1OaTkqWDtWLWduSEswLSFnM21PWHNQV0RHa3M6ME4kJkk6VW9aNEozQVAwYnNQUD4KLlFUQ0ArWlYkSnAzNyVhNSM3I1tnJ2FnbmpsWFk+WlFYJSxVaD9vWW9PIkZqP1w+KSJ1KzMvWTkyJFBDNiY3RFtRXWolJ0Vxa11gRyM5UykKODtPXkonX180N2lzRkFmSmVkKGtQV3M6SURBdTZRYi5pZj89PTw0UD85RVhmdE1hQCsoZUMiR0A/OVtEPDIrUVpuNk0vcidNT2dvazw5VSwKaW9pJz44byRDZGVYNkhrZ0R0RzUraURcLGg0b1JkPipadGVxXmRTMVssJ2JGK3JASTxlV08qbWNSQSNDJnFfJ1xfWl1qLFAndWlXIj5IcEwKYSJaMSs+czNhTTUtLTU2bjczIiwoOFolLTNBYDcqNUA8I2dbUnVxS0FiLV4rNGw5KDdFdHNeLEduaEhkU09KSl8iQWFrSkcvT2xUNDlabl4KQiElTTJZV01lPHA3R1ledT5ia0JEQy5LX0NzbXAjSF5qSG1wWWdhQmcoRkUzbDtLW08/XDxTOUNbbTRHUEJkPVBocyRNcWRiSU9CLmpHJiQKTCdDcVVccmJWZlJlRyo6YmhvaShkXCJXUElcVTYmKFNeViFWZmdpayRCY3QpMV0rKFwwSWdSSjAnYE1nUEREKWAsPmg2NVBmbVIuZUpqQmgKSVZpOzU8aDJURkI9OjZpIl4wR2YmPiJVSUdDXmdyb2FILWwnIldxXVs+UHIhXC1uKHJcL2tiKV1TSU5rMiFmSj5JL01UUSpfW21wW0RyZ2QKPUJGK19icktbMmBaPFk5cCopXWpcLTdcMFBERFMpLGhXRiJRWShAPi9sSSc/U0knXk9FJ0dFaS5AOC0hL1dyb0IoY2NPXnR1Z19tIlptbSwKYzFrTiFlJy45bE9KPWskY1hzQEEiQk5tJWxmZmpxbidcIyRATkltXyZLYExRZGdbYURAXClTaDVOMUNeNkFcSUteUzteOkU7NkkqZSd1QEwKLGUrVE1RPmRkPkFvO1xAcnVfW1s1YyFgT28hNCcma2hqTm9eVllYKzlWLHRQSyxcTUNGc1hoRWByJ1RpTF4xU0xPRlJyO2diLXNsO0BRXDgKVS8kVWlocT1NOy9qNEtVXF9TJVkxX1puIjJkSyZiXzg1ZyNXJmgoUnMuYVAuUVdfbFcjWF8vYTBXRzxkYl0lQCsjNW0/KSE9RSMkNFVAIWoKa1c0QUEoPmQtXjpDbyhEbnVPKF5fa0tRRlF0QWgjXmlzSis1ND1DcnFEbTlKQFQkRWU1cztQYyZHQmdfPENzdTwmRChEIT9zazovL1RWLGsKR1NMWi48JC8rK0dtKWFrVTg6biE+cklKRUlNI3A5RGhvKURGWzRXNUwtUyIiK2U1cUJuJVAwciJvVzNxbmwtcy0hYGlsRHJfT1JjMjY5QkIKM0crJ1khYElZYVQ7Nl89SS9jLlU6bThEPTM6VUBKLHViPGdEYipeNTMqV1YhVSdXczNFW0hJKWUsdSIsMS1YO1BMUEJKaCxoci4lPGdCOlgKX1lBOzkqPCZCOihFTWJIaWU2YSdXNzlIaS48TVw1IjtfbnByPmIrTDNWV1Q1XVhZUklKPEkxMCoxUVE7MERuS29UOzdEUy5nX00hVyc+dD8KRS8yQnBGbElJY2lqaHMmVWhVOW1iXyJHJkcnMCZeQjBxQEE6NiJlJlYoYSJRSGVVNTtuaUdINFhMKVpHOHUkJVtxUUFPZD4rP2Vaamw1UzkKM0EqQmhhSCZvVi9tYEFxM2hXNGQia0VsJSI1b0FMQVNgN04iITJyaT5LJzlgaGUubjMuXWJqVUVlLCsnX0REXlc2aVRXI1Y7biRqWWFoQSsKNC5uS110JV83VSomT3JvJ01mZ15SLT80TEZsWlwvKlA1MFgkLkg8Y1lTJEIxZDchK0NlMjEwNTlPJnBaM1BILSohajdpQjYvUyQoYDZaKk8KT10sQk5vVUtMWV9VW2Y4RzU3WGQiZWsxb0RvJVJyWl4vaiRyRCJZSTJLQG5jaEVQLEZFKixTKGImNFlPMkNNakpOdCRPMigvdWlOWC4zcioKbDl1a2tRPVNUbUNZbWEwXkg+RyJ1QiRzYktgJXNCUUdpVUxVZGIvK2wvbGJlajRsVSxeLj9fcjwpSl9XWVhqS1ZvRz0zKkM6KjxZVFwpbmwKMkNxUF9bPTFJInNdMEZsaGw7VSw5SXBkcTc0ODNbTEV0JSxzKk02LVcsSiZpckZLRSNFNSc2S1BPLUVLQDlvZFJJOmVqZCJiXFVqb2c1b28KIko4O1FIWWdrUF5wODRrWShodEpFcCUqTyJ1Rk5tV2Jfb2hVTGBrQzhqOkdMPEkmZTs+bUBWOCVKZCckam41QitAL1MnXi5vTE81REE8b0YKU0tJMF5ebVg6QmM7RTgjWVVRO2wyYzBZOERnISllL1J1YiNbVVk3cENDZDZAbEYpVEpGIXRacmctNCcjSkJQLW90U0pOSikjPzNebl4kZUoKTmZgXGVRR2ZQMS4xcSxxXytGJUtdUUcxIUEyWSdxTENKTElJdGQsJydLRlAzKkA/SCleQGhLIlo3RjkjNURMP3JWbmVmIiskakByMWclW1QKaFkwXyFQVEI3KDNQJ15kSWdJWEtcKyppZGVWVyJBR1wkcDI0R1crb2gvWnJpKmBeYyVzLmdATm4yN2FgLjM8MFA8WXMqZjVcKVZObXUqOioKcjpnXzYxRGFzbD0zOydXRmxaI0k0VWw2U0g2M2Q7S1pidW9EcmtdXz1dOyxFUkxRKGxGLilPRk9rLV0sIiFGQDkidDY/S2U+bHQ2KkEiPEsKRHA5Oj9OUFRzQWJuciQtdS4tN0YpNWBWbSZvOl8lPWsoSDdOWjhJRTk9Q0EvK2BpXSo4YUdFMCc0KUMjdFcmOXFMRGsxcWNCTXBVSSg9MV4KZ0lWXiRtciFrZmBqQCwhPDhgQzVLW3RGa2ppVipsdT4hMVdrdDAjaE0kQiJwcEMhX1FtWmZcaGhEYSdDJl5bRGQqRGBESCdkWEtfK28/MG8KKVxcQlUqPWVsQExaSnA3WEc1Q15dNmpOKyxrUm1mI2IkU108VjgjXk5eM1BZQCo5UCdYbj05cUpaUV9EbTRucTxFMiIvcFdsalRpJSVZMkMKVmZPSz4lZUZjb1YmOihIQiRDI1BqbixSXFVUYCpsbyxVU088TCZyX1wjJVpxIkxgLipvbiUvNkNKRGwxYEJSYCsjIik/b2RnS2ZIOzAzTjUKMl1OX2ZAJ3JgIVMvSSNwVE1wS11ecDYjZEc3cz9rSltBMSE8cSIkPDw3YEVqTE9nTEN1U0xrZWRlPUxWRSFBR0pmLVxAQFcxaWhkUlFiJiYKNVE6PSpNRXU8aSI7N2AjOF5FVitjIS9mTEFJaWBvInUhRGBgQHJTNC5UXlExUz43PFEzKUlLZTlDIVdfR2kvJUhNZl0lR1hLTVFHSkBxKjoKSSwlOChUQyhDKi5bNVRHJCYlXmpeJklDIkJWNVYnX3NWRFdEXytXcUxDSkpGZ1NnSSZbcGNTSiJjVjpucyYyNk07Ly9cSj10XydQRypEMisKJCtlPD5DOGpdME1HZHBlZUNRTScoX2svKGgpNzE8SE9JbitgMGU/Ql5zN2I3YjdXKWFrXk5NQy06aWNMLUNuTC81UEBBTC1EOCloUmEuSEMKZCZlWiI5XDMlIjUlMlFeP1A8bk5UWG1gMW9yQl00azw+TVAyRWNuNDRJMzw3YU4zbFlGIS0sYG0nSjYiKjZnTywhc0lZNV81T3I0M2UhQmYKOGA5MD42KjxsaE87ZC9uYTlxQCMtYyxtQnE8bFRGNjsqL0lrRjtaKyMoXlA2JWVAcjNMXUFDPDhjTCtwK1sqTj5vNGdtOW9TQzxMQG9XbVUKVmohUzBpITw1PjYqO0MzSyw4V2FhPGQ8bldQOVttMiNhKSk+ci5gKGpBLidgVSFWWjUlIlhIR25xMDtGblFTZ3JnKGtXZT9OZyxbMCFSYkYKaG01L0dJQnUmblNJN2Vka0xIZzkjdEZxQGAzNmMncl5oXC9UMidDISYrX1dLV0tLYWIuRiFfPmtCZkpKNTRuSC0xKm1JTSRlW0RJZlpMQFUKTytfOiZhVEZnMmpMZk9uQU5fMCoxMyM7clVrNGpJaEhrVV8vQjheXCMnW0UjcUgjQSNrZHViKnM0LGI2Img+WzBPQVglXnA8NjhyLWVzYEoKNT5iNExeIlJXX24qWG4vRmpUclFkK15dMV1aQC1vXGlUb0ZGUjQyOigmZ1wsbDMkN05tTDlXISguaUkoOTJoS2YxLyR1Y2coW2FeX2YsPUkKSy5fNDEhcWtUNSZVNXBUUkknOjMuPCRgLUBOWy9aaiMhYCNPNGFQJiw7czkwQEMpYWY5VDdwZmxodD5ZK2BcWFBPPG9qYSxjWlZTUiYzXiUKWGdTQzsudSw2LyokT11PISdyM2M9NVonT2xiUVhVXF0zYkNpZypqSigjVy5mT19TYW0kTE51bS85Uj9iR2wnVSFoVTxWRzpMUCIpa2xAcmUKLiEvZjhfaFxtPDYoRFNLazdaTjUyW0chcWNsSzFGWU9zNko1WU9wX24oOyFQSk1xOjJ1OCVaazEzUXJJQUMndWVjZFl0IVN1RGFbNTA8dEgKJSJscS5lJzJNKW4yZi1lIiFJLCszPicoXjYyQy8ocGZCcWdDYHFNWzUhKz4nN1EsPz0jWnIsPFUoZnQhcyhZP2EwYV00XlddSmo+Jl9YOiEKJidfK0NaRi1MMG9LK14pV2BaLF8yaSM5W1VFXVVBaFJTNT90OWUzNXAqPWRXdDFjU05fPzA5KzUsXVZKXFZhIVEtb0lfaUxaR3VSPTZUakQKUkNVSCdIX2M5PFBTKURiTDY6ZV4qWFVFI0c7U1VgaStqcTRXP1Irbm06SmFAIUZWMCxNYCVtPDQ6KUFhK0coXSZgXVBxcGReSWVLOTg6L0oKKkM8LVIwQSs9bmUoYSJxWD1eaV1PNiFZaHNvJmdpNnIoVGtKbF1maytMT049bk9OLT1qJiNGaGlpaUJXKVxPLTdmW0BlamY+bypTKCo0dG4KNyFiK20oc1hRMi5ZI2VwZy9YRiVkViQnPm41NTJcVlRHRz4lJE9ycDFWSzJXXXJLI1VcbDBFQS0kKSJALixxdE82Sl9acUxKMF5uYS9wRToKdGA2a2lvZnQ4MFNKPFhRaDRXaWkrTFslcVRyamxcSFZXI0kzb3N0YW1AX2FLQCRRaEluS2xXI2JGJDRPUyJ1biRDS2YuYFpoLlZjai5jXVUKX2I8bEBNTHVIZj9LSCEiMWxRNG4jayYoa29MLj1PRGl0cmNwOXI+RSJcSSg4YlgjJT1AIUQzSSNeQ2AoPD8qNDUxNmhgPVRDPzBgN2xzNE0KO2VPOVk/N3JvXC9hJjBMcXVOJ2U/OU0uS15bc2hnPmVcIiFSOiE1WUkyXVxscFU+QzRRTVc9TyhURXVgWEVTT246J1RfWjFGbWlqJ3E/IUQKVS8jN1M0I3FqLF5faDZkLWRHPDFWNkRgbjc9PVdBKzJSTVNOVSpPYVBUNy41JjYtVCNzQSw0OlZSYypPPCJuSV1MPDs8VD03MFpeSGhvY2AKU0lHNGwlXiowQ0FSKVg2TyQ5LkgzNEEiXnNVNnFqKyVrSCI0LjpDNlJcKW0vcilWdSg9MEw8Rk1RKHVwOVVhYyM9QlJnXjAzRmUhPDtUMT4KSm50QiVJcjBnMUFdQGVdLUVDYGRLXjUnZk9PVEJWTUU3b2xfbT5xXE1cLE1rUTlCJ1hxIklsOSEvbEIlLChZNylDL2VMbTNocm5iWipmZG4KTkosc1Q9ZEBgPS42RD9xSkg2XWQxO2E1S2coR0VgLjFAPFpTTk1GXmovbnQ9M19mVUYxNkZdXm9QPFFDbCltZmg+Zl1rZmAoajNgJk1XTCUKVWFvaHBkckZfSCUjPFlkWDdRSiJsVnMrJygiMiJxaiphT09HcEFiRlE+dChJREVcS2tlaDVuImpwMSdcVVBcRCxfZ2QrTVZRYjAmdCtdPSQKRT1MWy4nUz4/XiJqMDYvQFhIQVY/ajw4MyE8WFlKXyE9QEhFJ1M2JEJCbWAhIVlaRGVBcGspQSk7WDQ1JT1QUVFbaDlYSzRSRGNia21hWHEKc3VxbjNaXzY7KGRDSjNuOkJXVExabFdYS01jIXNYVDNkYi1kaiQqIj9zUitXSzUuSzw0NHByP10rUTRacCpnai1UM2BqTDNBKE1pPlsoJ2sKPUdicUcrRCNTO1lyXURKMWZYTEBTcnRzcEZpV0xfZlNrVzsja1xMXFxHLClBXnM7R29kcWE4VEZaLEY2ZmI6RDlBIk1KJWo2NGVfRV5HPmkKWTcnPC1DOCZbaysvX2EqRHBuTiZZPVVEbz87JmgkT2JEXUtvJ3JmR1IsK1hLKC9CVWRtcCFPcjIhLTcrLCNgKj45RV88TF82PlMlcXEpXV0KQj1DMC5nZlBGIXNWO19ZRSQzbDBjQlRhSF4kTDZfU0Vpc2U2S3VgN1Y4JycrazFfMFMmdEM8MDtPWm5WXV5AMTgpMyI4IyxoWSNXUihsczEKXTQjZG1PciNnMVxhTzM3Z0tKdFUtZyReRVJYbTxabzpvTCg+OjV0UmRTLUZVXjQvZ2dnI15BN1xzJEJyPipGTDUnaFJzIitAdEJdRERzXGwKXikhRTxaZEMtbl5DMGhGTl43cm44NmdcKSZtbCRaODNWQF1yO1wvRjA8TV1xLiVKZyY6TT5FJ2okbFhXRmBiJC9ycUZKclJQISxPbHRwMXAKO2dVI2VIT2wkJXJnL2ZMSChlW1YiSmNAIyhSXzlxUnBdL2BOXSVFZC9pOWJqNnMjY2czYytlIWwtRzZuTSExW0VEaCFDKWJOdE9ULHJXbj8KaFZMOF1bSmlrS0UuUCZqW09TIVxnWiN0SU5jdUMnSjM1LyYvdFRkai1dXyNUJ0wsJ1wpNGpHaExHb1dqZiY+SyhbSS91XkorRz1WVmtdJFIKMyZyXHInUzJaNSUmbmdIMFRxLGBROmdfJ0BvRiEoXDZaOkByKSdqP1JtZkI1JXRhUUhwQnBsb1w2b19bNjNsRTFuVVtMKGpRS0JRQkUkQUMKS05LKlhTZEspVDZtakMnalFxQDBLXmJHTENASmRoIlcoYkgnKVtSIm45KTVhbCk+XUk0VDsxUWlhdTttNFNYYl8jYUNHUU41QTs5VmBsbzIKPioqVUpZRWQtQSlZRTIvYmhVLDdIY0dWJUJlRzByYkI8NSZqYGklYHMiJ0k2aSc2LjUnInRmN0woNm8qcDgramxsbD4lYCpdZWhaTzM9Pm8KS2Bdb2hKcCxSOCQ4YnAiOypoYzRvQFJCa1FDTz9JVzooYU8wTTZWTFRjPEJlSixmVm5kQGYsZGZqOCZFYFJjKXJ0KlFkbj0vTTsrcVcsbDcKZlMjOS4vUzcmNHN0YDA7aSZfWV90OVdwITlhTVtpV3RURGlLSWI6Qi8mJ24hIzZLSyVpbWg/IjhARVhJNlxuLW0ucVFTO1czZ0NgTThWNzQKUHRTZ1xDRmpQYUtIYihhU0gzSDZfIywlSVpiJWYjWCQ7QUowT2FVa0ZdcSxKQGpzaTJqIj5tO0NSWmBxSC4qYipWZjImLDVjNFUiQGxULlYKRVNPSzpMKTZaO2BIOl0/VUpcTj8/O2lTJT1wXjdRYSNIamlUTHNVXTRBTCJiKC8wLj4+OiMpNkBGW0YhSV9gcHE4PituJiRycV1rWDExTzwKazRKQF8sIXRlKE1fXUovNSQ1L2ovW0QzRlU0XVBRcS00Pz9BKXE6MG4uNElZRDg7UVRoQzUrREtnNDMpYVBRQjo9OEs6aypxRSk8O1IxNE4KRjJyYmNUNGZDPDw4SyJVPyxxVVVNR0J1ajlmZCVPZUJAOVsmL1lKUjA+bCFIbSZaOyI3by9GVSZ1SlJlJ0o5YkVKO0Q4NG9ocGxOc0wwVDIKOEJhbkRzME9sNU5pNDMmOjJubk5BS1NyTVJdKzI1JSJrJWQiY148aG4rWzpKTyFWKENXJzpEcGdlWW88NTI+VUdOcjg2NldzQSZjYTE3bzgKcjVgYkY4SCdkI0o/X2JfWTA8WixHJSgtZHVRU0o/LjQhKzVTcWQ0M2ZgWWlYWmguJWQnZTo9JDNcXXUpZys4QVNGZWpfIyVIV2FDcj1rZ04KSzFHU2xIcitkT2w/QGc/akFwNjJfdHBsK19xRCwuMWNkT2ksLm5ucUQ6NmVXPU80STpQYkQ3J21zS1M2W09nYm1FT2VvYidDLGdUSyosTkoKMSZYZmxhVFYuXGcwRWwoVyZVaiVNMk83TypHMjgiWT8kZW9AXCRfP2s0TFdaMWUwcmQ+OmRhTzluVkpQMkprIionNTUzQGMvTVtrVGJ1VyEKbGpgQi1CPVdESFg0YlxZYmpVIUM8WjFaYDNsQGZvT0JMXWdkOmNfcUxBXmNDVE85WT9iaGojNzxqOm9ZRDUsL15VQiVuV0FXbE9PJVs0YWMKXV8hJWZZcWdTbSIiOTc3IUoqPCZHZ3A4OzYjXUhKcF1bTFciMCI0QjQzYS5MUTFFZyZaK0MuKV1ic186aFwxVFc2WFxra1E+RW46Y2FSZmoKQl4qITZIKiIrNmQ0RkhibF07P0RISTIxXSlZMycrZUVyImoxUEA3XmIuXEBoPiZzPSVHQytqRmhlLlhsS2lLWWcwK1UzIidjRz0xT1thKjUKcGZKcyspa0JMZ1AqSVJqa1Z1MHJkLUo+aUFqWEEhOWg9PjRuMkFDWSElMU1ubj5EXmYzXl1iImJDLUFxUTpIbG0qMCtKbig+TGdIdVs8YmAKIi8kVVU+UXUxNkMnam5DSGpjTjElJ0NeS2tHaUszcl83PDRYLTpfXylHaC9OUC5iYkRoMSlvIlgpPThsVDQmRF9AJWRBI2lMPmVxUUIqaD4KNjUtakc+Qjw/UWkvZTclc1kiaSpHIyFlOUcsOzxgb1w9RTVUX1xCJEBlMCFZSmZyUi9RaGsyLSJRV0tIVF1KUVBQbFxoaUovMjcvYVxsNiIKai9ePE50SF1oazBMcC4uKTFSSDtrPy9dMVdeZ3RyIz9aWiU0WFNGVDJNMFUmS0duT19BP1lGNmAodCokPUVANl5nV1csXDR1VXRcKi5QPV8Kb1ZXUjZgYl5yaCssZUBcY25odTc2JnNacUxFJ1k8ZGNnckYmKm1QSlpbTjhxNiU3MkI7WTJmXl5tXz1wRj8qUzQ2S19nckxaN05pQC1yYUkKNktuPG46QSpcQEhnLmllYDZYIm9SLTgsR0NXSWNMISxwPjAtb0VrUEMpXmIqblN0NWVoVlV0KCZEa1I3KHAmcklIXCkzJipWciVIUExRLjYKUWFNNjFSS1kqQFlTOzZxTERsNmhsRmlhX2xiUypJPiJYKEdVTiFOVT5DTzo/PWZAPms/IXRKP2JuP2hWcFA6TUc+O104K3NPTklcN1UxL0oKPWUrQGdjXidmUEZWISxrUTZKZW0qM2Y5OkxFLG8pWS9EQU5wJlgpcmE1Y01XZlBPOSsrOnBHI08iUEsjUDBMby9YYmNHUzE/LGowLSRtTj4KbWooSVpCPmpraTghaDAzNTJwY0cyPypiTmdzMFNuOVVFSS9QLC8uWWY1Pi1LKXA3RyhlXHNXNm0iKWZgdVZIbFpiQ20iSS9yVlJLWVE9ODAKZjUvYFRWdSdNa1FBTzQ9IScxaUkvTyUhZEVSTl9MUkEwRyRBKE9LJFEpL2ctXk48XV9uQS5HVG9udWsnIUgyVlUkUydSSGxaKCppXV1XN2gKVzA0MW4obTEwPiQuJl5namtSPys0I1dzMydcUVxtbTQsKTlbRCl0ZT1yQkZhNjVsTVtROHQrSTFTVFkzLVVccUs7NlFiXDEqOWVoMkMrUFEKOUp1RF5faipFPG4zWmlFU2ItbTdOWD09JHF0YmBuR1hgQFBkRFA7YTs8NS9dXGlxUjdYPlVVbkVPKCFLNWFNQ0hNPV5SX3ViPDg4JT02MkcKcVtLInFOLUticmNALSREJTpOQ0dfWEI7PEddSCErb20hJWBcbDROOCNvUyNeV19oc15VKzFGbSc1X1s4ZnU0cVVKaFtVWUBlM2pEXSNEMmkKPFNXLSE3MXQwZGEhaWNwYVNmRS9nSSUwJXU8RUIjOiQ6N0ZrUUZcYklaOUZIaV1jazs+OnE2NGYyLiJfbk1IdEAtZipGRlsvMSZuIydERFkKRSo1Vkk/PidOSVpEPjwxJiwzJCxrLyVdclRcNUwzOC9gaS9uJFlmNUBWR1QxJiolOW5lY1MvRkQuSCw5VCsvbyZMWDAlbjtaM2QwQlgvKV4KdSkuNy1SQlcuT3VzVGoxakRST2d1YTlQaWwhJCEvPT86dD85V2BrMj5ZQz5RKTBPZE9fRzNlIWQ3XnBsOXAmJmssITpGb0dgM0Q1QmhOZDoKT29uRE1WKEBTZkc1I1pKLmpuNSs2MURaRCkkS3Ryb0Y2VFBLN2FocDhYLFUtKlhXUCFtajZDcWUpSExIYl9hSSlvTUVSY2otQTY9byRQSlIKJ1pgPSxJbUokITdzSjpUKnRiVjduJEB0MjBePkhNVEllKVo7LDMhLEM+PVc/bHBFZWJcbDshI05UZmNfLG1BN3JgLU4vYDkqV2FDVyNVPCMKQ1EkJmEpTEJgXFlkY2UkUFxoTG1YY0pnciN1Wmw5dEEiWWkzYDU2bDFOcFZrRFZiTWQociNRXk4kYSNnXSw1ZFwzSDN1UXAwTmxTTV08bXIKYVllTCorJ1s+bkJVOzJqMD8/TE9IX2w/Oy5FT3QlcXAiWE0iMC1Ub1c0IzNcYlVNdTcxQSgvKTxncUltJSliLm4qOEMqS2c3ZDsjV3NTVysKT002IzMmJjhzW2JTSWstMFBHOVhMcENZMztHI28rV1U/L0RUX1NgSGdYK2wlZzJyJGVEOUlRZU1bJHFFSU8+W3MhIlcnLVE0OTlwblYoPXEKTEJhZWtcMF9rM0FxKy8hYDAxIUo9Z0cmbE1JKSIzc0VGOWJWJ0Q/Q3RpOT1sayNIIjpRWi9HSSFtOTtCXFBjbzBUZ2EwX2xtRFY6U0hUL10KYSIqMEhQRWhHJEk9S0kiNyk8L3IkOmE4YFU2cEotVjJPYmNKSk1eI0lNaVgtWmU0VUtgZTRBL0hHXy1BW1dFJkxJdENNS2MnRl9EdTwhIWQKLTpBM3EjaVlfNlFfSGJlanJVYS11U2AkLlVfPFpjcTI6PzAoZ3FbNzZVXjhVRTh0ays0LF5db0A1VVhSX2dzb2QqVj1dKnNgIVVXLzFFLi4KPXIvUiIiKT5dKVglOTAuJytITlFCPF5vck9uaWhUVilnP2tENzpFIVVbWDVlT01FSjBmJkpMUy0iLUxWbXRlaTBxW25DMXEuOUsjNz0yaVQKQ0RoIkgsJ01UKmVuVG5RWiRfaGtRRj5CYiYlNExTK3RaWl1KXCRjTFFlQUpWR0lQP1AxUElSYE5EQnNAUVpsZTdOdFokS2coKzMrJWRXWGYKREZfOSFlRVpGSDRQMUdVXDJpJ2kpUDQiQUk3USZXWCpoSyVPNlRIUGQxRmBAZnJUSURHWGRpLi5aVl1lSCkyMWRoWDghVi8oWmlpODxkU2oKZmBuSDU6NEs2Q2NHMDMoJDQybCIpZD9KJEdNSG9JPm4nSkkrUEQxJkEhQiRHM1pdIipaVkMiP0pFR3FTU3QncCJiSmtwU1Q5L0YmIT4sS08KcXFVKztdI1RAVmhWQ1BlaDszSyk+XmotN05aUyglZSdrZG8jQ25oQGZ1aVhqSEczXElIXUUtWiVePFw+OmY5P2AobzpIRGpNLVkqaSIkMkIKI29UZz9sLm8/MS0lJVouYjxPLXBCdVdDOTo/KlFfVzFLJDBMQDMyXGFTRDQlN1AkYUJXUFpnQHNGRjJLMzVOXV9sR1Bxa0NkZm8uPkwqWyoKKE0qSjx1I3NlVkUxYi0hISl0MklBbDViM1U0OUVmZEojZVtsSWpTJz1VKD0zbU5YalRiJG9GJSI2MXQpNWwzJFJFQW9yKCppbnRvVjFZUU8KLSkhclJCKGkna3AvXypuKCcpM0FbUiVSVFNJVmtAP2U0XTZfdD1YaDVVWVNRVUBhayEuOk5qT2M8JypxQ29URXBiP2RxJDonZiZBPjY4ZjgKOj4kXmdJdEdiY18yIl5uITs0VEApLUBHNjJfLVthRXM2ImEraHUhLiY3I2chQ0ldcVEwRFNQb3MlVTdETiI4dCg2SmgxQEklRiNNWnFzU2wKVTBxQlRWJS8rLEcscG5uIlVDMV9oaChMUVZPR2tjM09Qcj1qKzslRXR0KnItP1NgNVQ9RGdVNzsyMVMvPzp0L2ZIXiMsVU5iTzldZGhucikKdDdUamtRQTdFNzJJVGNeSWklZ1tWOWptJWJjXEZqaUU+cldkKD4iWWRUMCdxS1ErPldzIVpmImFyTUhdRFo+dTUrRFdSMCwxZU1dJmpBIkkKKDZgU2woXl5WbjRUU0xkM2tULGhfXCleQWIzR3MsWylBZko2Yy0kLD4mRy5JN1g9dG9qSlEuS21fOFFtLEMrcVg7VVp1bitgdEYqb3RlVTAKRzsqWG8pZkxqNXQsak5xMmtvKEVfUEU9b1pZI2BCNjRwKy5OJCo/XiE2KyU9IS1RQ2NqMDJfMDQlKThtSC9KNjcuUSgpKmIsWWRkJVIoZTQKRVBNRXJFT0ZdRmA8Ui5KYjBpbCNFODBBV15RQUlGbDtCUyZTcS0lT0RCczI1NydDO09oWTc0cHVfST9OcmBfYVtpLU5gTCVhOWIiJFg4TTUKLE1OYShoO0FWTEooInUiMjovNzRxYnVSaXI3SU5BKTsoUUdMW1RfUGxsLGwwMkdVNSw/VWNzK1JGVD5ILCdEVGtUWlA/Vypicj86cTA3dDUKYytNaScuPS5SZSU6K2REOVxbU004MGNQRTJjRVdWO0tPQDlgTSJVKylvOUZJJzY/YjhkYFo6LjEhMWRULnAlUy0zcV8nOFpaSEpYVm5jLykKIz9LO2NtQ2U9ZF83L09HTkcvV0wyVWcrUU4uUiUiTyZBbCk2KClhOWomV184bjpPWWtROm1AXiNTQGlkP3A2c2VgYW5XNlQ+QWJKTDNJXWQKOTVyVldfY15dLE5tdVUiU1VPP01jK3VZcDpdb3VxQExhXXIkcSpJXiVENnQnWmQtQzYmNVt0VzU7PjtHcS9CZFFoTmA6QDkwIiw1ISU8Yj0KMT1AISs6ZCU1XHI5WWNPakIiRU4pKCNxcHNfZkwwNStKbkJdNDIxbHMoaXMtaUxObF86cyk2SjU1WnFGI2hRNiROUCVqIzY0YCh6enp6elMKQjFeMVdSWDRzfj4KZW5kc3RyZWFtCmVuZG9iagp4cmVmCjAgMjUKMDAwMDAwMDAwMCA2NTUzNSBmIAowMDAwMDAwMDA5IDAwMDAwIG4gCjAwMDAwMDAwNTggMDAwMDAgbiAKMDAwMDAwMDEwNCAwMDAwMCBuIAowMDAwMDAwMTYyIDAwMDAwIG4gCjAwMDAwMDAyMTQgMDAwMDAgbiAKMDAwMDAwMDMxMiAwMDAwMCBuIAowMDAwMDAwNDE1IDAwMDAwIG4gCjAwMDAwMDA1MjEgMDAwMDAgbiAKMDAwMDAwMDYzMSAwMDAwMCBuIAowMDAwMDAwNzI3IDAwMDAwIG4gCjAwMDAwMDA4MjkgMDAwMDAgbiAKMDAwMDAwMDkzNCAwMDAwMCBuIAowMDAwMDAxMDQzIDAwMDAwIG4gCjAwMDAwMDExNDQgMDAwMDAgbiAKMDAwMDAwMTI0NCAwMDAwMCBuIAowMDAwMDAxMzQ2IDAwMDAwIG4gCjAwMDAwMDE0NTIgMDAwMDAgbiAKMDAwMDAwMTYyMiAwMDAwMCBuIAowMDAwMDAyMDg4IDAwMDAwIG4gCjAwMDAwMDYxNTkgMDAwMDAgbiAKMDAwMDAwNjgwNiAwMDAwMCBuIAowMDAwMDA3MDY3IDAwMDAwIG4gCjAwMDAwMDg2MDcgMDAwMDAgbiAKMDAwMDAxMzQyMSAwMDAwMCBuIAp0cmFpbGVyCjw8Ci9JbmZvIDE3IDAgUgovU2l6ZSAyNQovUm9vdCAxIDAgUgo+PgpzdGFydHhyZWYKMzc5MjIKJSVFT0YK";

        // Decode the Base64-encoded PDF
        // $decodedPDF = base64_decode($base64EncodedPDF);

        Storage::disk('public')->put('file001.pdf',base64_decode($base64EncodedPDF));

    }
}
