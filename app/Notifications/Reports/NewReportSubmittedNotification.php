<?php

namespace App\Notifications\Reports;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * A class defined for new report submitted message
 */
class NewReportSubmittedNotification extends Notification implements ShouldQueue
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
            ->subject('تم إرسال البلاغ بنجاح')
            ->view('team', [
                'title' => 'تم استلام البلاغ',
                'description' => 'شكرًا لك على إرسال بلاغك. نحن نتعامل مع جميع البلاغات بجدية وسنحقق في الأمر على الفور. سيراجع فريقنا التفاصيل ويتخذ الإجراء المناسب عند الحاجة.',
                'url' => 'https://moawen.sa',
                'button_title' => 'تواصل مع الدعم',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
