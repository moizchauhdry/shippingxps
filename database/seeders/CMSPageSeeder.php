<?php

namespace Database\Seeders;

use App\Models\CMSPage;
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
                'meta_title' => 'Services',
                'meta_description' => Lorem::paragraph(),
                'meta_keywords' => 'services'
            ],
            [
                'meta_title' => 'Shipping Calculator',
                'meta_description' => Lorem::paragraph(),
                'meta_keywords' => 'Shipping Calculator'
            ],
            [
                'meta_title' => 'How it works',
                'meta_description' => Lorem::paragraph(),
                'meta_keywords' => 'How it works'
            ],
            [
                'meta_title' => 'Store & Forward',
                'meta_description' => Lorem::paragraph(),
                'meta_keywords' => 'Store & Forward'
            ],
            [
                'meta_title' => 'Consolidation',
                'meta_description' => Lorem::paragraph(),
                'meta_keywords' => 'Consolidation'
            ],
            [
                'meta_title' => 'Online Shopping Assistant',
                'meta_description' => Lorem::paragraph(),
                'meta_keywords' => 'Online Shopping Assistant'
            ],
            [
                'meta_title' => 'Package returns',
                'meta_description' => Lorem::paragraph(),
                'meta_keywords' => 'Package returns'
            ],
            [
                'meta_title' => 'Customer Service',
                'meta_description' => Lorem::paragraph(),
                'meta_keywords' => 'Customer Service'
            ],
            [
                'meta_title' => 'Service Updates',
                'meta_description' => Lorem::paragraph(),
                'meta_keywords' => 'Service Updates'
            ],
            [
                'meta_title' => 'Unaccapted Items',
                'meta_description' => Lorem::paragraph(),
                'meta_keywords' => 'Unaccapted Items'
            ],
            [
                'meta_title' => 'Policies & Terms',
                'meta_description' => Lorem::paragraph(),
                'meta_keywords' => 'Policies & Terms'
            ],

        ]);
    }
}
