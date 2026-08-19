<?php

namespace App\Channels;

use Exception;
use Illuminate\Notifications\Notification;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FcmNotification;

/**
 * A class defines the FCM notification channel
 */
class FcmChannel
{
    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param Notification $notification
     *
     * @return void
     */
    public function send(mixed $notifiable, Notification $notification): void
    {
        try {
            $fcmToken = $notifiable->routeNotificationFor('fcm');

            if (!$fcmToken) {
                return;
            }

            if (!method_exists($notification, 'toFcm')) {
                return;
            }

            $messageData = $notification->toFcm($notifiable);

            if (empty($messageData)) {
                return;
            }

            $cloudMessage = CloudMessage::withTarget('token', $fcmToken)
                ->withNotification(FcmNotification::create(
                    $messageData['title'] ?? '',
                    $messageData['body'] ?? ''
                ));

            if (!empty($messageData['data'])) {
                $cloudMessage = $cloudMessage->withData($messageData['data']);
            }

            app('firebase.messaging')->send($cloudMessage);
            
        } catch (Exception $e) {
            // Log the exception but don't fail the entire notification flow
            \Log::error('FCM Push Notification Failed: ' . $e->getMessage());
        }
    }
}
