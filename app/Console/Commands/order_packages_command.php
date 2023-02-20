<?php

namespace App\Console\Commands;

use App\Models\Package;
use App\Models\PackageBox;
use App\Models\User;
use Illuminate\Console\Command;

class order_packages_command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order_packages:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $packages = Package::get();
        foreach ($packages as $key => $package) {
            $package->update(['package_handler_id' => NULL]);
            if (in_array($package->status, ['labeled', 'shipped'])) {
                $package->update([
                    'admin_status' => 'accepted',
                    'pkg_dim_status' => 'done',
                ]);
            }
        }

        $customers = User::query()
            ->where('type', 'customer')
            ->get();

        foreach ($customers as $key => $customer) {
            foreach ($customer->orders as $key => $order) {
                $package = Package::updateOrCreate(
                    [
                        'customer_id' => $customer->id,
                        'warehouse_id' => $order->warehouse_id,
                        'tracking_number_in' => $order->tracking_number_in,
                        'received_from' => 'NIL',
                        'notes' => 'NIL',
                        'status' => 'open',
                        'pkg_type' => 'single',
                        'pkg_dim_status' => 'done',
                        'admin_status' => 'accepted',
                    ]
                );

                $package->update([
                    'package_no' => $package->id,
                    'package_handler_id' => $package->id,
                ]);

                PackageBox::updateOrCreate(
                    [
                        'package_id' => $package->id
                    ],
                    [
                        'package_id' => $package->id,
                        'pkg_type' => $package->pkg_type,
                        'weight_unit' => $order->weight_unit,
                        'dim_unit' => $order->dim_unit,
                        'weight' => $order->package_weight,
                        'length' => $order->package_length,
                        'width' => $order->package_width,
                        'height' => $order->package_height,
                    ]
                );

                $order->update(['package_id' => $package->id]);
            }
        }
    }
}
