<?php

namespace App\Listeners;

use App\Events\OrderUpdatedEvent;
use App\Models\User;
use App\Notifications\OrderUpdated;
use App\Notifications\SendUserNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class OrderUpdatedListener
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
     * @param  OrderStatusChanged  $event
     * @return void
     */
    public function handle(OrderUpdatedEvent $event) {
        $order = $event->order;
        $user = User::find($order->customer_id);
        if ($user) {
            //$user->notify(new SendUserNotification($order));
            $order->notify(new OrderUpdated($order));
        }
    }
}
