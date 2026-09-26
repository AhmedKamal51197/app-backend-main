<?php

namespace App\Notifications\Welcome;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * A class defined for the welcome message
 */
class WelcomeNotification extends Notification implements ShouldQueue
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
            ->subject('مرحبًا بك في معاون!!')
            ->view('team', [
                'title' => 'مرحبًا بك في معاون',
                'description' => 'يسعدنا انضمامك إلينا. ابدأ بإكمال ملفك الشخصي واكتشف كيف يمكننا مساعدتك في إطلاق مشاريعك أو تقديم خدماتك بأمان وثقة كاملين. هل تحتاج إلى مساعدة؟ نحن هنا لدعمك في أي وقت',
                'url' => 'https://moawen.sa',
                'button_title' => 'أكمل ملفك الشخصي',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
