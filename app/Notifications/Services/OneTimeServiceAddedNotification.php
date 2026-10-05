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
            ->subject('تم استلام الخدمة المقطوعة وهي قيد المراجعة')
            ->view('team', [
                'title' => 'الخدمة قيد المراجعة',
                'description' => 'شكرًا لك! تم استلام خدمتك المقطوعة في معاون وهي الآن قيد المراجعة من قبل فريقنا. سنُعلمك فور اعتمادها ونشرها.',
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
