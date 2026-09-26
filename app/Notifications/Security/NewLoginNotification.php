<?php

namespace App\Notifications\Security;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

/**
 * A class defined for new login message
 */
class NewLoginNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Carbon $loginDate;
    protected string $loginTime;

    public function __construct(Carbon $loginDate, string $loginTime)
    {
        $this->loginDate = $loginDate;
        $this->loginTime = $loginTime;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('تسجيل دخول جديد إلى حسابك')
            ->view('team', [
                'title' => 'تم رصد تسجيل دخول جديد',
                'description' => "رصدنا تسجيل دخول جديد إلى حسابك في معاون بتاريخ {$this->loginDate->format('M d, Y')} الساعة {$this->loginTime}. إذا كان هذا أنت، فلا حاجة لأي إجراء. إذا لم تتعرّف على تسجيل الدخول هذا، فيرجى تأمين حسابك على الفور.",
                'url' => 'https://moawen.sa',
                'button_title' => 'مراجعة الأمان',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'login_date' => $this->loginDate->toDateString(),
            'login_time' => $this->loginTime,
        ];
    }
}
