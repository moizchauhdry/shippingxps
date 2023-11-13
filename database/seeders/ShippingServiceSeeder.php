<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShippingServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipping_services')->truncate();

        DB::table('shipping_services')->insert([
            // FEDEX 
            [
                "service_name" => 'FEDEX_GROUND',
                "service_code" => 'FEDEX_GROUND',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ],
            [
                "service_name" => 'FEDEX_INTERNATIONAL_CONNECT_PLUS',
                "service_code" => 'FEDEX_INTERNATIONAL_CONNECT_PLUS',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ],
            [
                "service_name" => 'FEDEX_INTERNATIONAL_PRIORITY_EXPRESS',
                "service_code" => 'FEDEX_INTERNATIONAL_PRIORITY_EXPRESS',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ],
            [
                "service_name" => 'GROUND_HOME_DELIVERY',
                "service_code" => 'GROUND_HOME_DELIVERY',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ],
            [
                "service_name" => 'INTERNATIONAL_ECONOMY',
                "service_code" => 'INTERNATIONAL_ECONOMY',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ],
            [
                "service_name" => 'INTERNATIONAL_PRIORITY_FREIGHT',
                "service_code" => 'INTERNATIONAL_PRIORITY_FREIGHT',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ],
            [
                "service_name" => 'STANDARD_OVERNIGHT',
                "service_code" => 'STANDARD_OVERNIGHT',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ],
            [
                "service_name" => 'FIRST_OVERNIGHT',
                "service_code" => 'FIRST_OVERNIGHT',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ],
            [
                "service_name" => 'FEDEX_INTERNATIONAL_PRIORITY',
                "service_code" => 'FEDEX_INTERNATIONAL_PRIORITY',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ],
            [
                "service_name" => 'PRIORITY_OVERNIGHT',
                "service_code" => 'PRIORITY_OVERNIGHT',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ],
            [
                "service_name" => 'FEDEX_2_DAY_AM',
                "service_code" => 'FEDEX_2_DAY_AM',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ],
            [
                "service_name" => 'FEDEX_2_DAY',
                "service_code" => 'FEDEX_2_DAY',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ],
            [
                "service_name" => 'FEDEX_EXPRESS_SAVER',
                "service_code" => 'FEDEX_EXPRESS_SAVER',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ],
            [
                "service_name" => 'INTERNATIONAL_FIRST',
                "service_code" => 'INTERNATIONAL_FIRST',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ],

            // DHL
            [
                "service_name" => 'EXPRESS_WORLDWIDE',
                "service_code" => 'EXPRESS_WORLDWIDE',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ],

            // UPS
            [
                "service_name" => 'UPS Next Day Air',
                "service_code" => '01',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ],
            [
                "service_name" => 'UPS 2nd Day Air',
                "service_code" => '02',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ],
            [
                "service_name" => 'UPS Ground',
                "service_code" => '03',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ],
            [
                "service_name" => 'UPS 3 Day Select',
                "service_code" => '12',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ],
            [
                "service_name" => 'UPS Next Day Air Saver',
                "service_code" => '13',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ],
            [
                "service_name" => 'UPS UPS Next Day Air Early',
                "service_code" => '14',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ],
            [
                "service_name" => 'UPS 2nd Day Air A.M. Valid international values',
                "service_code" => '59',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ],
            [
                "service_name" => 'UPS Worldwide Express',
                "service_code" => '07',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ],
            [
                "service_name" => 'UPS Worldwide Expedited',
                "service_code" => '08',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ],
            [
                "service_name" => 'UPS Standard',
                "service_code" => '11',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ],
            [
                "service_name" => 'UPS Worldwide Express Plus',
                "service_code" => '54',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ],
            [
                "service_name" => 'UPS Worldwide Saver',
                "service_code" => '65',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ],
            [
                "service_name" => 'UPS UPS Worldwide Express Freight',
                "service_code" => '96',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ],
            [
                "service_name" => 'UPS UPS Worldwide Express Freight Midday Required for Rating and ignored for Shopping',
                "service_code" => '71',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ],
        ]);
    }
}
