<?php

namespace App\Listeners;

use App\Events\OrderChangesByAdminEvent;
use App\Notifications\NotifyChangesByAdminToCustomer;
use App\Notifications\OrderCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class OrderChangesByAdminListener
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
    public function handle(OrderChangesByAdminEvent $event)
    {
        $order = $event->order;
        $user = $order->customer;
        if ($user) {
            $user->notify(new NotifyChangesByAdminToCustomer($order));
        }
    }
}
