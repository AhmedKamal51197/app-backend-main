<?php

namespace App\Notifications\Security;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * A class defined for password reset success message
 */
class PasswordResetSuccessNotification extends Notification implements ShouldQueue
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
            ->subject('Password Reset Successful')
            ->view('team', [
                'title' => 'Password Updated',
                'description' => 'Your password has been successfully reset. Your account is now secure with your new password. If you did not make this change, please contact our support team immediately.',
                'url' => 'https://moawen.sa/login',
                'button_title' => 'Access Account',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
