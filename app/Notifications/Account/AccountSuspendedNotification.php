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
            ->subject('تم تعليق حسابك')
            ->view('team', [
                'title' => 'تم تعليق الحساب!!',
                'description' => 'تم تعليق حسابك في معاون. إذا كنت تعتقد أن هذا حدث بالخطأ أو ترغب في استعادة حسابك، يرجى التواصل مع فريق الدعم لدينا. إذا لم تقم بأي نشاط مريب، يمكنك تجاهل هذه الرسالة بأمان. شكرًا لتفهمك، فريق معاون',
                'url' => 'https://api.whatsapp.com/send/?phone=%2B96566445995&text&type=phone_number&app_absent=0',
                'button_title' => 'تواصل معنا',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
