<?php

namespace App\Notifications\Payments;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * A class defined for payment request rejected message
 */
class PaymentRequestRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected float $amount;

    public function __construct(float $amount)
    {
        $this->amount = $amount;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Payment Request Rejected')
            ->view('team', [
                'title' => 'Payment Request Update',
                'description' => "We regret to inform you that your payment request for $" . number_format($this->amount, 2) . " has been rejected. Please review the requirements and resubmit if needed. Contact support for more details.",
                'url' => 'https://moawen.sa/wallet',
                'button_title' => 'View Payment Requests',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'amount' => $this->amount,
        ];
    }
}
