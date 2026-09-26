<?php

namespace App\Notifications\VerifyEmail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * A class defined for the email otp notification
 */
class VerifyEmailOtpNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $otp;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $otp)
    {
        $this->otp = $otp;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('تحقّق من بريدك الإلكتروني')
            ->view('team', [
                'title' => 'مرحبًا بك في معاون',
                'description' => 'تحقّق من بريدك الإلكتروني للوصول إلى ميزات معاون، رمز التحقق الخاص بك هو: ' . $this->otp,
                'url' => 'https://moawen.sa',
                'button_title' => 'تحقّق من بريدك',
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'otp' => $this->otp,
            'expires_at' => now()->addMinutes(5),
        ];
    }
}
