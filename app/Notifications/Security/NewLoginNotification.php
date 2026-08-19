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
            ->subject('New Login to Your Account')
            ->view('team', [
                'title' => 'New Login Detected',
                'description' => "We detected a new login to your Moawen account on {$this->loginDate->format('M d, Y')} at {$this->loginTime}. If this was you, no action is needed. If you don't recognize this login, please secure your account immediately.",
                'url' => 'https://moawen.sa',
                'button_title' => 'Review Security',
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
