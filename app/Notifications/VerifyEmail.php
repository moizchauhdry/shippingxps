<?php

namespace App\Notifications;

use App\Mail\CustomVerificationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;

class VerifyEmail extends BaseVerifyEmail
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        $verificationUrl = $this->verificationUrl($notifiable);
    
        // return (new MailMessage)
        //     ->subject('Verify Your Email Address')
        //     ->line('Please click the button below to verify your email address.')
        //     ->action('Verify Email Address', $verificationUrl)
        //     ->line('If you did not create an account, no further action is required.');

        $data = [
            'subject' => "Verify Email Address",
            'verification_url' => $verificationUrl,
        ];

        return (new CustomVerificationMail($data))
            ->to($notifiable->email)
            ->bcc('moizchauhdry01@gmail.com');
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
