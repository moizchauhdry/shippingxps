<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Illuminate\Http\Response;
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

class HomeController extends Controller
{

    public $token = 'm99PyQIqoq5GmAKjs1wOTAbhQ0ozkc0s';
    public $cookie = 'PHPSESSID=3rf796ooctiic30gq0e54bpg45';
    public $customer_id = '12339140';
    public $integration_id = '59690';



    public $service_list = [

        //use only fedex and dhl.
        //use logo as well.

        'fedex_international_economy' => [
            'carrierCode' => 'fedex',            
            'serviceCode' => 'fedex_international_economy',
            'packageTypeCode' => 'fedex_custom_package',
            'serviceLabel' =>'FedEx International Economy®',
            'currency' => 'USD',
            'totalAmount' => 0,
            'baseAmount' => 0,
            'isReady' => false,
            'logo'=> 'fedex-logo.png',
        ],
        'usps_international_first_class' => [
            'carrierCode' => 'usps',
            'serviceCode' => 'usps_international_first_class',
            'packageTypeCode' => 'usps_custom_package',
            'serviceLabel' =>'USPS International First Class',
            'currency' => 'USD',
            'totalAmount' => 0,
            'baseAmount' => 0,
            'isReady' => false,
            'logo'=> 'usps-logo.jpg',
        ],
        'usps_international_priority' => [
            'carrierCode' => 'usps',
            'serviceCode' => 'usps_international_priority',
            'packageTypeCode' => 'usps_custom_package',
            'serviceLabel' =>'USPS International Priority',
            'currency' => 'USD',
            'totalAmount' => 0,
            'baseAmount' => 0,
            'isReady' => false,
            'logo'=> 'usps-logo.jpg',
        ],
        'usps_international_express' => [
            'carrierCode' => 'usps',
            'serviceCode' => 'usps_international_express',
            'packageTypeCode' => 'usps_custom_package',
            'serviceLabel' =>'USPS International Express',
            'currency' => 'USD',
            'totalAmount' => 0,
            'baseAmount' => 0,
            'isReady' => false,
            'logo'=> 'usps-logo.jpg',
        ],
        'dhl_express_worldwide' => [
            'carrierCode' => 'dhl',
            'serviceCode' => 'dhl_express_worldwide',
            'packageTypeCode' => 'dhl_custom_package',
            'serviceLabel' =>'DHL Intl Express',
            'currency' => 'USD',
            'totalAmount' => 0,
            'baseAmount' => 0,
            'isReady' => false,
            'logo'=> 'dhl-logo.png',
        ],

    ];

    public function dashboard() {

        $customer_id =Auth::user()->id;
        $user_type = Auth::user()->type;

        $query = Order::with('warehouse')->where('status','=','arrived');

        if($user_type == 'customer'){
            $query->where('customer_id','=',$customer_id);
        }

        $arrived = $query->limit(5)->get();

        $query = Package::where('status','=','labeled');

        if($user_type == 'customer'){
            $query->where('customer_id','=',$customer_id);
        }

        $labeled = $query->limit(5)->get();

        $query = Package::where('status','=','shipped');

        if($user_type == 'customer'){
            $query->where('customer_id','=',$customer_id);
        }

        $shipped = $query->limit(5)->get();


        //$labeled = Order::where('status','=','labeled')->limit(5)->get();
        //$shipped = Order::where('status','=','shipped')->limit(5)->get();
        //$rejected = Order::where('status','=','rejected')->limit(5)->get();

//        $notifications = \auth()->user()->unreadNotifications()->limit(3)->get() ;

        return Inertia::render('Dashboard',[
            'arrived' => $arrived,
            'labeled' => $labeled,
            'shipped' => $shipped
        ]);
    }

    public function pricingTable(){
        return Inertia::render('PricingTable');

    }

    public function pricing(){

        $countries = Country::all(['id','nicename as name'])->toArray();

        $warehouses = Warehouse::all()->toArray();

        return Inertia::render('Pricing',[
            'countries' => $countries,
            'warehouses' => $warehouses,
            'services' => $this->service_list
        ]);
    }


    public function shopping(){


        $user = Auth::user();

        $profile_complete = TRUE;
        //check if profile is complete
        if(empty($user->first_name) ||
            empty($user->last_name) ||
            empty($user->address1) ||
            empty($user->country) ||
            empty($user->state) ||
            empty($user->city) ||
            empty($user->postal_code) ||
            empty($user->phone_no) ||
            empty($user->email)
        ){
            $profile_complete = FALSE;
        }

        $cities_from = City::where('country_code_2','US')->get()->toArray();
        $cities_to = City::where('country_code_2','!=','US')->get()->toArray();


        return Inertia::render('Shopping',[
            'cities_from' => $cities_from,
            'cities_to' => $cities_to,
            'profile_complete' => $profile_complete
        ]);
    }

    public function getQuote(Request $request){

        $service = $request->get('service');

        $service = json_decode($service);

        $markup = SiteSetting::getByName('markup');

        $ship_from= $request->input('ship_from');
        $ship_to = $request->input('ship_to');
        $weight = $request->input('weight');
        $units = $request->input('weight_unit','lb_in');
        $length = $request->input('length');
        $width = $request->input('width');
        $height = $request->input('height');
        $zipcode = $request->input('zipcode');
        //$declared_value = $request->input('declared_value');

        $declared_value = '1.0';

        $warehouse = Warehouse::where('id',$ship_from)->first();
        
        $country = Country::where('id',$ship_to)->first();

        $units = explode('_',$units);

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
            "zip" => empty($warehouse->zip) ? 'null': $warehouse->zip,
        ];

        $receiver = [
            "country" => $country->iso,
            "zip" => empty($zipcode) ? 'null': $zipcode,
        ];

        $pieces = [
            0 => [
                "weight"=> $weight,
                "length"=> $length,
                "width"=> $width,
                "height"=> $height,
                "insuranceAmount"=> null,
                "declaredValue"=> $declared_value
            ]
        ];

        $post_params = [
            "carrierCode" => $service->carrierCode,
            "serviceCode" =>  $service->serviceCode,
            "packageTypeCode" => $service->packageTypeCode,
            "sender" => $sender,
            "receiver" => $receiver,
            "residential" => true,
            "signatureOptionCode" => "DIRECT",
            "contentDescription" => "stuff and things",
            "weightUnit" => $weight_unit,
            "dimUnit" => $dimention_unit,
            "currency"=> "USD",
            "customsCurrency"=> "USD",
            "pieces"  => $pieces,
            "billing" => [
                "party" => "receiver"
            ]
        ];
        $service_rate = [];

        try {

            $client = new Client();

            $request = $client->post('https://xpsshipper.com/restapi/v1/customers/'.$this->customer_id.'/quote', [
                'headers' => $headers,
                'body' => json_encode($post_params),
                'http_errors' => true,
            ]);

            $response = $request ? $request->getBody()->getContents() : null;

            $response = json_decode($response);

            $markup_amount = $response->totalAmount*((int)$markup/100);

            $total = $response->totalAmount+ $markup_amount;

            $total = number_format($total,2);

            $service_rate = [
                "carrierCode" => $service->carrierCode,
                'serviceLabel' => $service->serviceLabel,
                'serviceCode' => $service->serviceCode,
                "packageTypeCode" => $service->packageTypeCode,
                'currency' => $response->currency,
                'totalAmount' => $total,
                'baseAmount' => $response->baseAmount,
                'isReady' => true,
                'logo'=> $service->logo,
            ];

        }catch(\Exception $ex){

            $ex_message = $ex->getMessage();

            $pos = strpos($ex_message,'{"error":"');

            $pos1 = strpos($ex_message,'"errorCategory"');
            $length = $pos1-($pos+12);

            $message = substr($ex_message,$pos+10,$length);

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
    
    public function getQuotes(Request $request){
      
      $markup = SiteSetting::getByName('markup');
      $ship_from= $request->input('ship_from');
      $ship_to = $request->input('ship_to');
      $weight = $request->input('weight');
      $units = $request->input('weight_unit','lb_in');
      $length = $request->input('length');
      $width = $request->input('width');
      $height = $request->input('height');
      $zipcode = $request->input('zipcode');
      //$declared_value = $request->input('declared_value');
      $declared_value = '1.0';
      $warehouse = Warehouse::where('id',$ship_from)->first();
      $country = Country::where('id',$ship_to)->first();
      $units = explode('_',$units);
      $weight_unit = isset($units[0]) ? $units[0] : 'lb';
      $dimention_unit = isset($units[1]) ? $units[1] : 'in';
      $headers = [
          'cache-control' => 'no-cache',
          'Content-Type' => 'application/json',
          'Authorization' => 'Bearer ' . $this->token
      ];
      $client = new Client();
      
      $request = $client->get('https://xpsshipper.com/restapi/v1/customers/'.$this->customer_id.'/services', [
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
          "weight"=> $weight,
          "length"=> $length,
          "width"=> $width,
          "height"=> $height,
          "insuranceAmount"=> null,
          "declaredValue"=> $declared_value
        ]
      ];
      foreach($response->services as $service){
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
                "currency"=> "USD",
                "customsCurrency"=> "USD",
                "pieces"  => $pieces,
                "billing" => [
                  "party" => "receiver"
                ]
            ];
            try {
              $request = $client->post('https://xpsshipper.com/restapi/v1/customers/'.$this->customer_id.'/quote', [
                  'headers' => $headers,
                  'body' => json_encode($post_params),
                  'http_errors' => true,
              ]);
        
              $response = $request ? $request->getBody()->getContents() : null;
              $response = json_decode($response);
              $markup_amount = $response->totalAmount*((int)$markup/100); 
              $total = $response->totalAmount+$markup_amount;
              $total = number_format($total,2);
              $service_rates[] = [
                'label' => $service->serviceLabel,
                'serviceCode' => $service->serviceCode,
                'currency' => $response->currency,
                //'totalAmount' => $response->totalAmount,
                'totalAmount' => $total,
                'baseAmount' => $response->baseAmount,
              ];
            }catch(\Exception $ex){
                
              $ex_message = $ex->getMessage();
              $pos = strpos($ex_message,'{"error":"');
                      
              $pos1 = strpos($ex_message,'"errorCategory"');
              $length = $pos1-($pos+12);
      
              $message = substr($ex_message,$pos+10,$length);
      
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
    



    private function getPackageTypeCode($service){

        $type = "";

        switch($service->carrierCode){
            case "dhl" :
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

    public function notifications() {
        
        $notifications = \auth()->user()->notifications()->orderBy('created_at', 'DESC')->get();

        return Inertia::render('Notifications',[
            'notifications' => $notifications
        ]);
    }

    public function markAllRead() {
        \auth()->user()->unreadNotifications->markAsRead();
        return redirect('notifications');
    }


    public function markRead(Request $request){

        $id = $request->input('notification_id');
        
        $notification = auth()->user()->notifications()->where('id', $id)->first();

        if($notification){
            $notification->markAsRead();
        }
        
        return redirect('notifications');

    }
}
