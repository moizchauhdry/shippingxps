<?php

namespace App\Notifications;

use App\Models\Package;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PackageShippingServiceNotification extends Notification
{
    use Queueable;

    public $package;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Package $package)
    {
        $this->package = $package;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $url = route("packages.show", ["id" => $this->package->id]);
        return [
            'package_id' => $this->package->id,
            'message' => $this->package->customer->name . ' #' . $this->package->customer->suite_no . ' has selected shipping service 
            <strong>' . $this->package->service_code . '</strong> and payment is pending, Grand Total,
            <strong> $' . $this->package->grand_total . '</strong> for <a class="link-primary" href="' . $url . '" >Package # ' . $this->package->id . '</a>',
            'url' => $url
        ];
    }
}
