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
            ->subject('Verify Your Email Address')
            ->view('team', [
                'title' => 'Welcome to Moawen',
                'description' => 'Verify your email address to access Moawen features, Your OTP is: ' . $this->otp,
                'url' => 'https://moawen.sa',
                'button_title' => 'Verify you email',
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
