<?php

namespace App\Listeners;

use App\Events\ShoppingCreatedEvent;
use App\Models\User;
use App\Notifications\ShoppingCreatedNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ShoppingCreatedListener
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
     * @param  ShoppingCreated  $event
     * @return void
     */
    public function handle(ShoppingCreatedEvent $event)
    {
        $admins = User::where(['type' => 'admin'])->get();
        Notification::send($admins, new ShoppingCreatedNotification($event->order));
    }
}