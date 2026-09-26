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
            ->subject('فعّل حسابك')
            ->view('team', [
                'title' => 'مرحبًا بك في معاون',
                'description' => 'لتأمين حسابك وفتح جميع الميزات، يرجى النقر على الزر أدناه لتفعيل حسابك في معاون. إذا لم تقم بإنشاء هذا الحساب، فلا تتردد في تجاهل هذا البريد. شكرًا لثقتك، فريق معاون',
                'url' => 'https://moawen.sa',
                'button_title' => 'تفعيل الحساب',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
