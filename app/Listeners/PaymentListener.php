<?php

namespace App\Listeners;

use App\Events\PaymentEventHandler;
use App\Models\Order;
use App\Models\Package;
use App\Models\User;
use App\Notifications\NotifyOrderChangesAcceptedByCustomerToAdmin;
use App\Notifications\PaymentNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PaymentListener
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
    public function handle(PaymentEventHandler $event)
    {
        $payment= $event->payment;
        $admins = User::where('type','admin')->get();
        \Notification::send($admins,new PaymentNotification($payment));

        /*$customer = $payment->customer;
        if($customer != null)
        {
            $customer->notify(new PaymentEventHandler($payment));
        }*/

    }
}
