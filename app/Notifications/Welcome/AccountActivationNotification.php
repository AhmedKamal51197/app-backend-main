<?php

namespace App\Notifications\Welcome;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * A class defined for the account activation message
 */
class AccountActivationNotification extends Notification implements ShouldQueue
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
            ->subject('Activate Your Account')
            ->view('team', [
                'title' => 'Welcome to Moawen',
                'description' => 'To secure your account and unlock all features, please click the button below to activate your Moawen account. If you didnt create this account, feel free to ignore this email. Thanks for your trust, Moawen Team',
                'url' => 'https://moawen.sa',
                'button_title' => 'Activate Account',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
