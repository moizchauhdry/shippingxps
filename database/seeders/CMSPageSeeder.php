<?php

namespace Database\Seeders;

use App\Models\CMSPage;
use Carbon\Carbon;
use Faker\Provider\Lorem;
use Illuminate\Database\Seeder;

class CMSPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        CMSPage::Insert([
            [
                'title' => 'Services',
                'meta_title' => 'Services',
                'meta_description' => Lorem::paragraph(),
                'description' => Lorem::paragraph(),
                'meta_keywords' => 'services',
                'created_at' => Carbon::now(),
            ],
            [
                'title' => 'Shipping Calculator',
                'meta_title' => 'Shipping Calculator',
                'meta_description' => Lorem::paragraph(),
                'description' => Lorem::paragraph(),
                'meta_keywords' => 'Shipping Calculator',
                'created_at' => Carbon::now(),
            ],
            [
                'title' => 'How it works',
                'meta_title' => 'How it works',
                'meta_description' => Lorem::paragraph(),
                'description' => Lorem::paragraph(),
                'meta_keywords' => 'How it works',
                'created_at' => Carbon::now(),
            ],
            [
                'title' => 'Store & Forward',
                'meta_title' => 'Store & Forward',
                'meta_description' => Lorem::paragraph(),
                'description' => Lorem::paragraph(),
                'meta_keywords' => 'Store & Forward',
                'created_at' => Carbon::now(),
            ],
            [
                'title' => 'Consolidation',
                'meta_title' => 'Consolidation',
                'meta_description' => Lorem::paragraph(),
                'description' => Lorem::paragraph(),
                'meta_keywords' => 'Consolidation',
                'created_at' => Carbon::now(),
            ],
            [
                'title' => 'Online Shopping Assistant',
                'meta_title' => 'Online Shopping Assistant',
                'meta_description' => Lorem::paragraph(),
                'description' => Lorem::paragraph(),
                'meta_keywords' => 'Online Shopping Assistant',
                'created_at' => Carbon::now(),
            ],
            [
                'title' => 'Package returns',
                'meta_title' => 'Package returns',
                'meta_description' => Lorem::paragraph(),
                'description' => Lorem::paragraph(),
                'meta_keywords' => 'Package returns',
                'created_at' => Carbon::now(),
            ],
            [
                'title' => 'Customer Service',
                'meta_title' => 'Customer Service',
                'meta_description' => Lorem::paragraph(),
                'description' => Lorem::paragraph(),
                'meta_keywords' => 'Customer Service',
                'created_at' => Carbon::now(),
            ],
            [
                'title' => 'Service Updates',
                'meta_title' => 'Service Updates',
                'meta_description' => Lorem::paragraph(),
                'description' => Lorem::paragraph(),
                'meta_keywords' => 'Service Updates',
                'created_at' => Carbon::now(),
            ],
            [
                'title' => 'Unaccapted Items',
                'meta_title' => 'Unaccapted Items',
                'meta_description' => Lorem::paragraph(),
                'description' => Lorem::paragraph(),
                'meta_keywords' => 'Unaccapted Items',
                'created_at' => Carbon::now(),
            ],
            [
                'title' => 'Policies & Terms',
                'meta_title' => 'Policies & Terms',
                'meta_description' => Lorem::paragraph(),
                'description' => Lorem::paragraph(),
                'meta_keywords' => 'Policies & Terms',
                'created_at' => Carbon::now(),
            ],

        ]);
    }
}
