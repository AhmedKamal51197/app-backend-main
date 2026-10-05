<?php

namespace App\Notifications\Services;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent to the service owner once an admin approves the service, so the
 * "published / now live" message is only delivered after the review passes.
 */
class ServiceApprovedNotification extends Notification implements ShouldQueue
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
            ->subject('خدمتك الآن متاحة للعملاء')
            ->view('team', [
                'title' => 'تم نشر الخدمة',
                'description' => 'أخبار رائعة! تم اعتماد خدمتك وأصبحت الآن متاحة ومرئية للعملاء المحتملين على معاون. ابدأ في استقبال الطلبات ونمِّ عملك معنا.',
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
