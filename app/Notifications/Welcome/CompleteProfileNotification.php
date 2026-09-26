<?php

namespace App\Notifications\Welcome;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * A class defined for the complete profile
 */
class CompleteProfileNotification extends Notification implements ShouldQueue
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
            ->subject('أكمل ملفك الشخصي')
            ->view('team', [
                'title' => 'مرحبًا بعودتك!',
                'description' => "لمساعدتنا في مطابقتك مع أفضل الفرص وبناء الثقة، يرجى إكمال ملفك الشخصي بنسبة 100%. كلما كان ملفك الشخصي أكثر اكتمالًا ووضوحًا، زادت ظهوريتك! نحن دائمًا هنا من أجلك، فريق معاون",
                'url' => 'https://moawen.sa',
                'button_title' => 'أكمل ملفك الشخصي الآن',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
