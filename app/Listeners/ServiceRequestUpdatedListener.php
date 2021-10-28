<?php

namespace App\Listeners;

use App\Events\ServiceRequestUpdatedEvent;
use App\Models\User;
use App\Notifications\ServiceRequestUpdatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ServiceRequestUpdatedListener
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
     * @param  object  $event
     * @return void
     */
    public function handle(ServiceRequestUpdatedEvent $event)
    {
        $service_request = $event->service_request;
        $user = User::find($service_request->package->customer_id);

        if ($user) {
            $user->notify(new ServiceRequestUpdatedNotification($service_request));
        }
        
    }
}
