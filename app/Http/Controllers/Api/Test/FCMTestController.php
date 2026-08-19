<?php

namespace App\Http\Controllers\Api\Test;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\Notification\FCMService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FCMTestController extends BaseApiController
{
    protected $fcmService;

    public function __construct(FCMService $fcmService)
    {
        $this->fcmService = $fcmService;
    }

    /**
     * Test sending a notification.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function sendTestNotification(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
            'title' => 'required|string',
            'body' => 'required|string',
        ]);

        $success = $this->fcmService->sendNotification(
            $request->token,
            $request->title,
            $request->body,
            ['click_action' => 'FLUTTER_NOTIFICATION_CLICK']
        );

        if ($success) {
            return $this->jsonSuccess(null, 'Notification sent successfully');
        }

        return $this->jsonError('Failed to send notification. Check logs for details.');
    }
}
