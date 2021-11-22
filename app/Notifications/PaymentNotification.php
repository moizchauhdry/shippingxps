<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentNotification extends Notification
{
    use Queueable;
    public $payment;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($payment)
    {
        //
        $this->payment = $payment;
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
        if($this->payment->package_id != null){
            $url = route("packages.show",$this->payment->package_id);
            return [
                'package_id' => $this->payment->package_id,
                'message' => 'Customer has paid for <strong>Package '.$this->payment->package_id.'</strong>, See Details  <a class="link-primary" href="'.$url.'" >Package # '.$this->payment->package_id.'</a>',
                'url' =>$url
            ];
        }else{
            $url = route("shop-for-me.edit",$this->payment->order_id);
            return [
                'order_id' => $this->payment->order_id,
                'message' => 'Customer has paid for <strong>Order '.$this->payment->order_id.'</strong>, Click on link to complete shipping <a class="link-primary" href="'.$url.'" >Order # '.$this->payment->order_id.'</a>',
                'url' =>$url
            ];
        }
    }
}
