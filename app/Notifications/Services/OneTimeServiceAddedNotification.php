<?php

namespace App\Notifications\Services;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * A class defined for one-time service added successfully message
 */
class OneTimeServiceAddedNotification extends Notification implements ShouldQueue
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
            ->subject('تمت إضافة الخدمة المقطوعة بنجاح')
            ->view('team', [
                'title' => 'تم نشر الخدمة',
                'description' => 'مبروك! تمت إضافة خدمتك المقطوعة بنجاح إلى معاون. أصبحت خدمتك الآن متاحة ومرئية للعملاء المحتملين. ابدأ في استقبال الطلبات ونمِّ عملك معنا.',
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
