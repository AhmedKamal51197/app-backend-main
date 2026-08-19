<?php

namespace App\Http\Controllers\Api\Chat;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\Chat\OpenChatRequest;
use App\Http\Resources\Api\Chat\ChatResource;
use App\Models\Chat;
use App\Models\Setting;
use App\Services\Chat\ChatService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * A class defines the chat controller
 */
class ChatController extends BaseApiController
{
    /**
     * Load the service
     *
     * @param ChatService $service
     */
    public function __construct(protected ChatService $service)
    {
    }

    /**
     * List of the user accessible chats
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $chats = $this->service->index(
            request()->user(),
            $request->input('limit', Setting::PAGE_RESULT_LIMIT)
        );

        return $this->jsonSuccess(ChatResource::collection($chats));
    }

    /**
     * Show the chat with all messages
     *
     * @param Chat $chat
     *
     * @return JsonResponse
     */
    public function show(Chat $chat): JsonResponse
    {
        // Check if user is participant
        if (!$chat->participants()->where('user_id', request()->user()->id)->exists()) {
            return $this->jsonError(__('You do not have access to this chat'), 403);
        }

        $chat->load(['participants', 'chattable', 'messages.sender', 'messages.attachments']);

        return $this->jsonSuccess(ChatResource::make($chat));
    }

    /**
     * Open or create chat
     *
     * @param OpenChatRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function openChat(OpenChatRequest $request): JsonResponse
    {
        $chat = $this->service->openChat(request()->user(), $request->validated());

        return $this->jsonSuccess(
            ChatResource::make($chat),
            __('Chat opened successfully')
        );
    }
}
