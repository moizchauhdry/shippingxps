<?php

namespace App\Notifications;

use App\Mail\UserGeneralMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class AnnouncementNotification extends Notification
{
    use Queueable;
    public $customer, $description;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($customer)
    {
        $this->customer = $customer;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        // return (new MailMessage)
        //     ->subject('Important Announcement!')
        //     ->greeting('Dear ' . $customer_name)
        //     ->line('Effective December 1, 2024, the storage policy will be updated as follows:')
        //     ->line('* Storage days will change from 75 days to 30 days.')
        //     ->line('* A storage fee will apply to packages held for more than 30 days.')
        //     ->line('* Packages remaining beyond 35 days will be terminated and become the property of ShippingXPS.')
        //     ->line('* For the five-day period between 30 and 35 days, a daily fee will be charged');

        $this->description = "
            <p style='margin-top:10px'><strong>Effective December 1, 2024, the storage policy will be updated as follows:</strong></p>
            <ul>
                <li>Storage days will change from <strong>75 days to 30 days</strong>.</li>
                <li>A <strong>storage fee</strong> will apply to packages held for more than 30 days.</li>
                <li>Packages remaining beyond <strong>35 days</strong> will be terminated and become the property of ShippingXPS.</li>
                <li>For the <strong>five-day period</strong> between 30 and 35 days, a daily fee will be charged.</li>
            </ul>
        ";

        $data = [
            'subject' => "Storage Policy Notification",
            'name' => $this->customer->name,
            'description' => $this->description,
        ];

        Log::info($this->customer->name . "...send emails step 03");
        
        return (new UserGeneralMail($data))
            ->to($notifiable->email)
            ->cc('info@shippingxps.com')
            ->bcc('moizchauhdry@gmail.com');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
