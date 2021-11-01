<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('warehouses')->insert([            
            [
                'name' =>'California USA',
                'country_id' => 226,
                'zip' => '90011'
            ],
            [
                'name' => 'Tax Free Delaware USA',
                'country_id' => 226,
                'zip' => '19706'
            ]
        ]);
    }
}
