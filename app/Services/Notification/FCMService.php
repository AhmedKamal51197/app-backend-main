<?php

namespace App\Services\Notification;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class FCMService
{
    protected $client;
    protected $fcmUrl = 'https://fcm.googleapis.com/fcm/send'; // Legacy URL, easier for quick testing if they have a server key. 
    // Recommended: https://fcm.googleapis.com/v1/projects/{project-id}/messages:send for v1.

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * Send notification to a specific token.
     * 
     * @param string $token
     * @param string $title
     * @param string $body
     * @param array $data
     * @return bool
     */
    public function sendNotification(string $token, string $title, string $body, array $data = []): bool
    {
        $serverKey = env('FCM_SERVER_KEY');

        if (!$serverKey) {
            Log::error('FCM Server Key is not set in .env');
            return false;
        }

        try {
            $response = $this->client->post($this->fcmUrl, [
                'headers' => [
                    'Authorization' => 'key=' . $serverKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'to' => $token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                        'sound' => 'default',
                    ],
                    'data' => $data,
                    'priority' => 'high',
                ],
            ]);

            return $response->getStatusCode() === 200;
        } catch (\Exception $e) {
            Log::error('FCM Notification Error: ' . $e->getMessage());
            return false;
        }
    }
}
