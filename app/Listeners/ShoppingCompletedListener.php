<?php

namespace App\Listeners;

use App\Events\ShoppingCompletedEvent;
use App\Models\Order;
use App\Notifications\ShoppingCompletedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ShoppingCompletedListener
{
    public $order;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Handle the event.
     *
     * @param  ShoppinngCompletedEvent  $event
     * @return void
     */
    public function handle(ShoppingCompletedEvent $event)
    {
        $order = $event->order;
        $customer = $order->customer;

        if ($customer) {
            $customer->notify(new ShoppingCompletedNotification($order));
        }
    }
}
