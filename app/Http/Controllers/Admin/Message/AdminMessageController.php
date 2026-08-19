<?php

namespace App\Http\Controllers\Admin\Message;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Http\Requests\Api\Chat\StoreMessageRequest;
use App\Http\Resources\Api\Message\MessageResource;
use App\Models\Chat;
use App\Services\Message\MessageService;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * A class defines the admin message controller
 */
class AdminMessageController extends BaseAdminController
{
    /**
     * Load the service
     *
     * @param MessageService $service
     */
    public function __construct(protected MessageService $service)
    {
    }

    /**
     * Store message data
     *
     * @param Chat $chat
     * @param StoreMessageRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function store(Chat $chat, StoreMessageRequest $request): JsonResponse
    {
        $message = $this->service->store(request()->user(), $chat, $request->validated());

        return $this->jsonSuccess(
            MessageResource::make($message),
            __('Message sent successfully')
        );
    }
}
