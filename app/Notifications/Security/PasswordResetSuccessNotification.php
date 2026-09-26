<?php

namespace App\Notifications\Security;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * A class defined for password reset success message
 */
class PasswordResetSuccessNotification extends Notification implements ShouldQueue
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
            ->subject('تمت إعادة تعيين كلمة المرور بنجاح')
            ->view('team', [
                'title' => 'تم تحديث كلمة المرور',
                'description' => 'تمت إعادة تعيين كلمة مرورك بنجاح. أصبح حسابك الآن آمنًا بكلمة المرور الجديدة. إذا لم تقم بهذا التغيير، فيرجى التواصل مع فريق الدعم لدينا على الفور.',
                'url' => 'https://moawen.sa/login',
                'button_title' => 'الدخول إلى الحساب',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
