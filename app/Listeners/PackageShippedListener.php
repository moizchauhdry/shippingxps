<?php

namespace App\Listeners;

use App\Events\PackageShipped;
use App\Notifications\PackageShippedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PackageShippedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  PackageShipped  $event
     * @return void
     */
    public function handle(PackageShipped $event)
    {
        $package = $event->package;
        $customer = $package->customer;

        if ($customer) {
            $customer->notify(new PackageShippedNotification($package));
        }
    }
}
