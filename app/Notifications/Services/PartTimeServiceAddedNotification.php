<?php

namespace App\Notifications\Services;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * A class defined for part-time service added successfully message
 */
class PartTimeServiceAddedNotification extends Notification implements ShouldQueue
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
            ->subject('تمت إضافة الخدمة بدوام جزئي بنجاح')
            ->view('team', [
                'title' => 'الخدمة بدوام جزئي متاحة الآن',
                'description' => 'أحسنت! تم نشر خدمتك بدوام جزئي بنجاح على معاون. يمكن للعملاء الآن اكتشافك والتعاقد معك للمشاريع المستمرة. استعد لبناء علاقات مهنية طويلة الأمد.',
                'url' => 'https://moawen.sa/profile',
                'button_title' => 'عرض خدماتي',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
