<?php

namespace App\Http\Controllers\Admin\Chat;

use App\Enums\ChatTypeEnum;
use App\Http\Controllers\Admin\BaseAdminController;
use App\Http\Requests\Admin\Chat\ChatIndexRequest;
use App\Http\Requests\Admin\Chat\OpenChatRequest;
use App\Http\Resources\Admin\Chat\ChatResource;
use App\Models\Chat;
use App\Services\Chat\AdminChatService;
use App\Services\Chat\ChatService;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * A class defines the admin chat controller
 */
class AdminChatController extends BaseAdminController
{
    /**
     * Load the service
     *
     * @param AdminChatService $adminService
     * @param ChatService $service
     */
    public function __construct(protected AdminChatService $adminService, protected ChatService $service )
    {
    }

    /**
     * List all chats with filters
     *
     * @param ChatIndexRequest $request
     *
     * @return JsonResponse
     */
    public function index(ChatIndexRequest $request): JsonResponse
    {
        $chats = $this->adminService->index($request->validated());

        return $this->jsonSuccess(ChatResource::collection($chats));
    }

    /**
     * Show single chat details
     *
     * @param Chat $chat
     *
     * @return JsonResponse
     */
    public function show(Chat $chat): JsonResponse
    {
        $chat->load(['participants', 'messages.sender', 'messages.attachments', 'order']);

        if ($chat->type->value === ChatTypeEnum::PROJECT->value) {
            $projectOwnerId = $chat->chattable->user_id;
            $freelancerId = $chat->participants->first(function ($participant) use ($projectOwnerId) {
                return $participant->id !== $projectOwnerId;
            })?->id;

            $chat->load(['chattable.user']);

            if ($freelancerId) {
                $chat->chattable->load([
                    'proposals' => function ($query) use ($freelancerId) {
                        $query->where('user_id', $freelancerId)->with(['user', 'attachments']);
                    }
                ]);
            }
        } elseif ($chat->type->value === ChatTypeEnum::SERVICE->value){
            $chat->order?->load(['orderable']);
        }

        return $this->jsonSuccess(ChatResource::make($chat));
    }

    /**
     * Open or create chat (admin can chat with any user)
     *
     * @param OpenChatRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function openChat(OpenChatRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['type'] = ChatTypeEnum::USER->value;

        $chat = $this->service->openChat(request()->user(), $data);

        return $this->jsonSuccess(
            ChatResource::make($chat),
            __('Chat opened successfully')
        );
    }
}
