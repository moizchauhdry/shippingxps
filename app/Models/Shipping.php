<?php

namespace App\Models;


class Shipping
{
    
    public $token = 'm99PyQIqoq5GmAKjs1wOTAbhQ0ozkc0s';
    public $cookie = 'PHPSESSID=3rf796ooctiic30gq0e54bpg45';
    public $customer_id = '12339140';
    public $integration_id = '59690';

    public static function getShippingServices(){

        return [

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
    
    }

}
