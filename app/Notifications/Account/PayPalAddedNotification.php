<?php

namespace App\Notifications\Account;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * A class defined for PayPal added successfully message
 */
class PayPalAddedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('PayPal Account Added Successfully')
            ->view('team', [
                'title' => 'PayPal Account Connected',
                'description' => 'Excellent! Your PayPal account has been successfully connected to your Moawen profile. You can now receive payments through PayPal with ease and security.',
                'url' => 'https://moawen.sa/wallet',
                'button_title' => 'View Payment Methods',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
