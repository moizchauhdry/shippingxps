<?php

namespace App\Listeners;

use App\Events\OrderUpdatedEvent;
use App\Mail\UserGeneralMail;
use App\Models\User;
use App\Notifications\OrderUpdated;
use App\Notifications\SendUserNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

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

            $data = [
                'subject' => 'Order Item Updated',
                'name' => $user->name,
                'description' => '<p> Order ID #"'.$event->order->id.'" has been updated </p>',
            ];

            try{
                Mail::to($user)->send(new UserGeneralMail($data));
            }catch(\Throwable $e){
                \Log::info($e);
            }
        }
    }
}
