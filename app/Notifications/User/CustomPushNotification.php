<?php

namespace App\Notifications\User;

use App\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * A class defines the custom push notification
 */
class CustomPushNotification extends Notification
{
    use Queueable;

    protected string $title;
    protected string $body;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $title, string $body)
    {
        $this->title = $title;
        $this->body = $body;
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
        return [FcmChannel::class];
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
            'title' => $this->title,
            'body' => $this->body,
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
            'title' => $this->title,
            'body' => $this->body,
            'data' => [
                'type' => 'custom_notification',
            ]
        ];
    }
}
