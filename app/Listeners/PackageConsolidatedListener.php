<?php

namespace App\Listeners;

use App\Events\PackageConsolidated;
use App\Notifications\PackageConsolidatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PackageConsolidatedListener
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
     * @param  PackageConsolidated  $event
     * @return void
     */
    public function handle(PackageConsolidated $event)
    {
        $package = $event->package;
        $customer = $package->customer;

        if ($customer) {
            $customer->notify(new PackageConsolidatedNotification($package));
        }
    }
}
