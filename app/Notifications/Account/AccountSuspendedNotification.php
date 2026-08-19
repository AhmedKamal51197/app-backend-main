<?php

namespace App\Notifications\Account;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * A class defined for the account suspended message
 */
class AccountSuspendedNotification extends Notification implements ShouldQueue
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
            ->subject('Your Account Suspended')
            ->view('team', [
                'title' => 'Account Suspended!!',
                'description' => 'Your TeamWork account has been suspended. If you believe this is a mistake or want to restore your account, please contact our support team. If you did not perform any suspicious activity, you can safely ignore this message. Thank you for your understanding, TeamWork Team',
                'url' => 'https://api.whatsapp.com/send/?phone=%2B96566445995&text&type=phone_number&app_absent=0',
                'button_title' => 'Contact Us',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
