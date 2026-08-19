<?php

namespace App\Notifications\VerifyEmail;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends VerifyEmail
{
    protected function verificationUrl($notifiable)
    {
        // Generate the custom verification URL for the frontend
        $temporarySignedUrl = URL::temporarySignedRoute(
            'verification.verify', // Backend route name for verification
            now()->addMinutes(60), // Expiration time
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        // Replace the backend URL with the frontend one
        return str_replace(
            url('/api/email/verify'),
            'http://localhost:8000/api/verify-email/verify',
            $temporarySignedUrl
        );
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(__('Verify Email Address'))
            ->line(__('Click the button below to verify your email address.'))
            ->action(__('Verify Email Address'), $this->verificationUrl($notifiable))
            ->line(__('If you did not create an account, no further action is required.'));
    }
}
