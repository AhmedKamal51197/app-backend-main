<?php

namespace App\Services\Message;

use App\Actions\Attachments\StoreAttachmentAction;
use App\Actions\Files\GuessFileTypeAction;
use App\Enums\AttachmentDocumentTypeEnum;
use App\Enums\AttachmentStorageEnum;
use App\Enums\MessageTypeEnum;
use App\Events\LogExceptionEvent;
use App\Models\Chat;
use App\Models\Message;
use App\Models\Setting;
use App\Models\User;
use App\Services\Setting\SettingService;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * A class defines the message service
 */
class MessageService
{
    /**
     * Get chat messages with advanced filtering
     *
     * @param Chat $chat
     * @param int $perPage
     * @param array $filters
     *
     * @return LengthAwarePaginator
     */
    public function index(Chat $chat, int $perPage = Setting::PAGE_RESULT_LIMIT, array $filters = []): LengthAwarePaginator
    {
        return Message::query()
            ->with(['sender', 'replyTo.sender', 'attachments'])
            ->where('chat_id', $chat->id)
            ->when(isset($filters['type']), function (Builder $query) use ($filters) {
                $query->where('type', $filters['type']);
            })
            ->when(isset($filters['sender_id']), function (Builder $query) use ($filters) {
                $query->where('sender_id', $filters['sender_id']);
            })
            ->when(isset($filters['date_from']), function (Builder $query) use ($filters) {
                $query->where('created_at', '>=', $filters['date_from']);
            })
            ->when(isset($filters['date_to']), function (Builder $query) use ($filters) {
                $query->where('created_at', '<=', $filters['date_to']);
            })
            ->when(isset($filters['has_attachments']), function (Builder $query) use ($filters) {
                if ($filters['has_attachments']) {
                    $query->whereHas('attachments');
                } else {
                    $query->whereDoesntHave('attachments');
                }
            })
            ->when(isset($filters['search']), function (Builder $query) use ($filters) {
                $query->where('content', 'like', '%' . $filters['search'] . '%');
            })
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Store new message
     *
     * @param User $user
     * @param Chat $chat
     * @param array $data
     *
     * @return Message
     *
     * @throws Exception
     */
    public function store(User $user, Chat $chat, array $data): Message
    {
        DB::beginTransaction();
        try {
            // Validate user is participant
            if (!$chat->participants()->where('user_id', $user->id)->exists()) {
                throw new Exception(__('You are not a participant in this chat'));
            }

            // Create the message pinned to the chat
            $message = Message::create([
                'chat_id' => $chat->id,
                'sender_id' => $user->id,
                'content' => $data['content'] ?? null,
                'type' => $data['type'] ?? MessageTypeEnum::TEXT->value,
                'reply_to_id' => $data['reply_to_id'] ?? null,
                // Pin message to chat using morph relation
                'messageable_type' => Chat::class,
                'messageable_id' => $chat->id,
            ]);

            // Handle attachments if provided
            if (isset($data['attachments'])) {
                StoreAttachmentAction::store($message, $data['attachments'], 'attachments', false);
            }

            $chat->update(['last_message_at' => now()]);

            DB::commit();

            return $message->load(['sender', 'replyTo.sender', 'attachments', 'messageable']);

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Update message
     *
     * @param Message $message
     * @param array $data
     *
     * @return Message
     *
     * @throws Exception
     */
    public function update(Message $message, array $data): Message
    {
        DB::beginTransaction();
        try {
            // Only allow editing text messages
            if ($message->type !== MessageTypeEnum::TEXT) {
                throw new Exception(__('Only text messages can be edited'));
            }

            // Check if message is older than 24 hours
            if ($message->created_at->diffInHours(now()) > 24) {
                throw new Exception(__('Messages older than 24 hours cannot be edited'));
            }

            $message->update([
                'content' => $data['content'] ?? $message->content,
                'is_edited' => true,
                'edited_at' => now(),
            ]);

            DB::commit();

            return $message->load(['sender', 'replyTo.sender', 'attachments', 'messageable']);

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Delete message
     *
     * @param Message $message
     *
     * @return bool
     *
     * @throws Exception
     */
    public function delete(Message $message): bool
    {
        DB::beginTransaction();
        try {
            // Delete associated attachments from storage
            if ($message->attachments->count() > 0) {
                foreach ($message->attachments as $attachment) {
                    if (Storage::disk($attachment->disk)->exists($attachment->path)) {
                        Storage::disk($attachment->disk)->delete($attachment->path);
                    }
                }
            }

            $result = $message->delete();

            DB::commit();

            return $result;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Search messages across all user chats
     *
     * @param User $user
     * @param string $query
     * @param int $perPage
     * @param array $filters
     *
     * @return LengthAwarePaginator
     */
    public function searchMessages(User $user, string $query, int $perPage = Setting::PAGE_RESULT_LIMIT, array $filters = []): LengthAwarePaginator
    {
        return Message::query()
            ->with(['sender', 'chat', 'attachments'])
            ->whereHas('chat.participants', function (Builder $q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->where('content', 'like', '%' . $query . '%')
            ->when(isset($filters['chat_id']), function (Builder $q) use ($filters) {
                $q->where('chat_id', $filters['chat_id']);
            })
            ->when(isset($filters['type']), function (Builder $q) use ($filters) {
                $q->where('type', $filters['type']);
            })
            ->when(isset($filters['date_from']), function (Builder $q) use ($filters) {
                $q->where('created_at', '>=', $filters['date_from']);
            })
            ->when(isset($filters['date_to']), function (Builder $q) use ($filters) {
                $q->where('created_at', '<=', $filters['date_to']);
            })
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Get unread messages for user in chat
     *
     * @param Chat $chat
     * @param User $user
     *
     * @return Collection
     */
    public function getUnreadMessages(Chat $chat, User $user): Collection
    {
        $participant = $chat->participants()->where('user_id', $user->id)->first();

        if (!$participant) {
            return collect();
        }

        return Message::query()
            ->with(['sender', 'attachments'])
            ->where('chat_id', $chat->id)
            ->where('sender_id', '!=', $user->id)
            ->where('created_at', '>', $participant->pivot->last_read_at ?? $participant->pivot->joined_at)
            ->orderBy('created_at')
            ->get();
    }

    /**
     * Get messages by type
     *
     * @param Chat $chat
     * @param MessageTypeEnum $type
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public function getMessagesByType(Chat $chat, MessageTypeEnum $type, int $perPage = Setting::PAGE_RESULT_LIMIT): LengthAwarePaginator
    {
        return Message::query()
            ->with(['sender', 'attachments'])
            ->where('chat_id', $chat->id)
            ->where('type', $type->value)
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Get messages in date range
     *
     * @param Chat $chat
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public function getMessagesInDateRange(Chat $chat, Carbon $startDate, Carbon $endDate, int $perPage = Setting::PAGE_RESULT_LIMIT): LengthAwarePaginator
    {
        return Message::query()
            ->with(['sender', 'attachments'])
            ->where('chat_id', $chat->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Get messages with attachments
     *
     * @param Chat $chat
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public function getMessagesWithAttachments(Chat $chat, int $perPage = Setting::PAGE_RESULT_LIMIT): LengthAwarePaginator
    {
        return Message::query()
            ->with(['sender', 'attachments'])
            ->where('chat_id', $chat->id)
            ->whereHas('attachments')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Get messages by user
     *
     * @param Chat $chat
     * @param User $user
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public function getMessagesByUser(Chat $chat, User $user, int $perPage = Setting::PAGE_RESULT_LIMIT): LengthAwarePaginator
    {
        return Message::query()
            ->with(['sender', 'attachments'])
            ->where('chat_id', $chat->id)
            ->where('sender_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Forward message to another chat
     *
     * @param Message $message
     * @param Chat $targetChat
     * @param User $user
     *
     * @return Message
     *
     * @throws Exception
     */
    public function forwardMessage(Message $message, Chat $targetChat, User $user): Message
    {
        DB::beginTransaction();
        try {
            // Validate user is participant in target chat
            if (!$targetChat->participants()->where('user_id', $user->id)->exists()) {
                throw new Exception(__('You are not a participant in the target chat'));
            }

            $forwardedMessage = Message::create([
                'chat_id' => $targetChat->id,
                'sender_id' => $user->id,
                'content' => $message->content,
                'type' => $message->type,
                // Pin forwarded message to target chat
                'messageable_type' => Chat::class,
                'messageable_id' => $targetChat->id,
            ]);

            // Copy attachments if any
            if ($message->attachments->count() > 0) {
                $disk = SettingService::getAttachmentStorage();
                foreach ($message->attachments as $attachment) {
                    // Copy file to new location
                    $originalPath = $attachment->path;
                    $newPath = 'message_attachments/' . $forwardedMessage->id . '/' . basename($originalPath);

                    Storage::disk($disk)->copy($originalPath, $newPath);

                    // Create new attachment record
                    $forwardedMessage->attachments()->create([
                        'disk' => $disk,
                        'path' => $newPath,
                        'file_meta' => $attachment->file_meta,
                        'user_id' => $user->id,
                        'document_type' => $attachment->document_type,
                        'type' => $attachment->type,
                    ]);
                }
            }

            // Update target chat's last message timestamp
            $targetChat->update(['last_message_at' => now()]);

            DB::commit();

            return $forwardedMessage->load(['sender', 'attachments', 'messageable']);

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Bulk delete messages
     *
     * @param Chat $chat
     * @param array $messageIds
     * @param User $user
     *
     * @return int
     *
     * @throws Exception
     */
    public function bulkDeleteMessages(Chat $chat, array $messageIds, User $user): int
    {
        DB::beginTransaction();
        try {
            $messages = Message::query()
                ->where('chat_id', $chat->id)
                ->whereIn('id', $messageIds)
                ->where('sender_id', $user->id) // Only allow deleting own messages
                ->get();

            $deletedCount = 0;

            foreach ($messages as $message) {
                // Delete associated attachments from storage
                if ($message->attachments->count() > 0) {
                    foreach ($message->attachments as $attachment) {
                        if (Storage::disk($attachment->disk)->exists($attachment->path)) {
                            Storage::disk($attachment->disk)->delete($attachment->path);
                        }
                    }
                }

                $message->delete();
                $deletedCount++;
            }

            DB::commit();

            return $deletedCount;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Get chat statistics
     *
     * @param Chat $chat
     *
     * @return array
     */
    public function getChatStatistics(Chat $chat): array
    {
        $totalMessages = $chat->messages()->count();
        $totalParticipants = $chat->participants()->count();

        $messagesByType = Message::query()
            ->where('chat_id', $chat->id)
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        $messagesByUser = Message::query()
            ->with('sender:id,name')
            ->where('chat_id', $chat->id)
            ->selectRaw('sender_id, COUNT(*) as count')
            ->groupBy('sender_id')
            ->get()
            ->map(function ($item) {
                return [
                    'user' => $item->sender->name,
                    'count' => $item->count
                ];
            })
            ->toArray();

        $messagesPerDay = Message::query()
            ->where('chat_id', $chat->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        return [
            'total_messages' => $totalMessages,
            'total_participants' => $totalParticipants,
            'messages_by_type' => $messagesByType,
            'messages_by_user' => $messagesByUser,
            'messages_per_day' => $messagesPerDay,
            'first_message_date' => $chat->messages()->oldest()->first()?->created_at?->format('Y-m-d H:i:s'),
            'last_message_date' => $chat->messages()->latest()->first()?->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Create system message
     *
     * @param Chat $chat
     * @param string $content
     * @param array $data
     *
     * @return Message
     *
     * @throws Exception
     */
    public function createSystemMessage(Chat $chat, string $content, array $data = []): Message
    {
        DB::beginTransaction();
        try {
            $message = Message::create([
                'chat_id' => $chat->id,
                'sender_id' => $chat->created_by, // Use chat creator as sender for system messages
                'content' => $content,
                'type' => MessageTypeEnum::TEXT->value,
                // Pin system message to chat
                'messageable_type' => Chat::class,
                'messageable_id' => $chat->id,
            ]);

            // Update chat's last message timestamp
            $chat->update(['last_message_at' => now()]);

            DB::commit();

            return $message->load(['sender']);

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Export chat messages
     *
     * @param Chat $chat
     * @param User $user
     * @param string $format
     *
     * @return array
     *
     * @throws Exception
     */
    public function exportMessages(Chat $chat, User $user, string $format = 'json'): array
    {
        // Validate user is participant
        if (!$chat->participants()->where('user_id', $user->id)->exists()) {
            throw new Exception(__('You are not a participant in this chat'));
        }

        $messages = Message::query()
            ->with(['sender', 'attachments'])
            ->where('chat_id', $chat->id)
            ->orderBy('created_at')
            ->get();

        $exportData = [
            'chat' => [
                'id' => $chat->id,
                'title' => $chat->title,
                'type' => $chat->type->value,
                'created_at' => $chat->created_at->format('Y-m-d H:i:s'),
            ],
            'messages' => $messages->map(function ($message) {
                return [
                    'id' => $message->id,
                    'sender' => $message->sender->name,
                    'content' => $message->content,
                    'type' => $message->type->value,
                    'attachments' => $message->attachments->map(function ($attachment) {
                        return [
                            'name' => json_decode($attachment->file_meta, true)['original_name'] ?? 'Unknown',
                            'type' => $attachment->type,
                            'size' => json_decode($attachment->file_meta, true)['size'] ?? 0,
                        ];
                    }),
                    'created_at' => $message->created_at->format('Y-m-d H:i:s'),
                ];
            }),
            'exported_at' => now()->format('Y-m-d H:i:s'),
            'exported_by' => $user->name,
        ];

        return $exportData;
    }
}
