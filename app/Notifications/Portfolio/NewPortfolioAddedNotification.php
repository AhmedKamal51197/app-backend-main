<?php

namespace App\Notifications\Portfolio;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * A class defined for new portfolio added message
 */
class NewPortfolioAddedNotification extends Notification implements ShouldQueue
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
            ->subject('تمت إضافة عنصر لمعرض الأعمال بنجاح')
            ->view('team', [
                'title' => 'تم تحديث معرض الأعمال',
                'description' => 'ممتاز! تمت إضافة عنصر جديد إلى معرض أعمالك في ملفك الشخصي بنجاح. اعرض أفضل أعمالك لجذب المزيد من العملاء وإبراز خبرتك. واصل بناء معرض أعمالك المميز.',
                'url' => 'https://moawen.sa/profile',
                'button_title' => 'عرض معرض الأعمال',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
