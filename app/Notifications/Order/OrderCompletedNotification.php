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
            ->subject('تم إنجاز طلبك!')
            ->view('team', [
                'title' => 'تم إنجاز الطلب!',
                'description' => "مبروك! تم إنجاز طلبك " . $this->code . " لـ " . $this->name .  " بنجاح. يمكنك الآن ترك تقييم وتحرير الدفعة.",
                'url' => "https://moawen.sa/en/order-history/",
                'button_title' => 'المراجعة والإكمال',
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
            'title' => 'تم إنجاز الطلب!',
            'body' => "مبروك! تم إنجاز طلبك " . $this->code . " لـ " . $this->name . " بنجاح.",
            'data' => [
                'code' => $this->code,
                'name' => $this->name,
                'type' => 'order_completed',
            ]
        ];
    }
}
