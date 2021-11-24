<?php

namespace App\Listeners;

use App\Events\OrderCreatedEvent;
use App\Mail\UserGeneralMail;
use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderCreatedNotification;
use App\Notifications\SendUserNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class OrderCreatedListener
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
     * @param  OrderCreated  $event
     * @return void
     */
    public function handle(OrderCreatedEvent $event) {
        $order = $event->order;        
        $user = $order->customer;
        if ($user) {
            $user->notify(new OrderCreatedNotification($order));
        }

        $data = [
            'subject' => 'Order Item Created',
            'name' => $user->name,
            'description' => '<p> Order ID #"'.$event->order->id.'" created by admin </p>',
        ];

        try{
            Mail::to($user)->send(new UserGeneralMail($data));
        }catch(\Throwable $e){
            \Log::info($e);
        }
    }
}
