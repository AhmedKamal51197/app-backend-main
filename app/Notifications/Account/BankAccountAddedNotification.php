<?php

namespace App\Notifications\Account;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * A class defined for bank account added successfully message
 */
class BankAccountAddedNotification extends Notification implements ShouldQueue
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
            ->subject('Bank Account Added Successfully')
            ->view('team', [
                'title' => 'Bank Account Added',
                'description' => 'Great news! Your bank account has been successfully added to your Moawen profile. You can now receive payments directly to your bank account. All transactions are secure and protected.',
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
