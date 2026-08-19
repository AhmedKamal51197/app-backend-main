<?php

namespace App\Notifications\Order;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * A class defined the order completed notification
 */
class OrderCompletedNotification extends Notification
{
    use Queueable;

    protected string $code;
    protected string $name;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $code, string $name)
    {
        $this->code = $code;
        $this->name = $name;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param object $notifiable
     *
     * @return array
     */
    public function via(object $notifiable): array
    {
        return ['mail', \App\Channels\FcmChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Order Has Been Completed!')
            ->view('team', [
                'title' => 'Order Completed!',
                'description' => "Congratulations! Your order " . $this->code . " for " . $this->name .  " has been successfully completed. You can now leave feedback and release the payment.",
                'url' => "https://moawen.sa/en/order-history/",
                'button_title' => 'Review & Complete',
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param object $notifiable
     *
     * @return array
     */
    public function toArray(object $notifiable): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
        ];
    }

    /**
     * Get the FCM representation of the notification.
     *
     * @param object $notifiable
     *
     * @return array
     */
    public function toFcm(object $notifiable): array
    {
        return [
            'title' => 'Order Completed!',
            'body' => "Congratulations! Your order " . $this->code . " for " . $this->name . " has been successfully completed.",
            'data' => [
                'code' => $this->code,
                'name' => $this->name,
                'type' => 'order_completed',
            ]
        ];
    }
}
