<?php

namespace App\Services\Chat;

use App\Enums\ChatTypeEnum;
use App\Events\LogExceptionEvent;
use App\Models\Chat;
use App\Models\Job;
use App\Models\Project;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Team;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * A class defines the chat service
 */
class ChatService
{
    /**
     * Get user accessible chats
     *
     * @param User $user
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public function index(User $user, int $perPage = Setting::PAGE_RESULT_LIMIT): LengthAwarePaginator
    {
        return Chat::query()
            ->with(['participants', 'chattable', 'messages' => function ($query) {
                $query->latest()->limit(1);
            }])
            ->where('is_active', true)
            ->whereHas('participants', function (Builder $query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->orderByDesc('last_message_at')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Open or create chat
     *
     * @param User $user
     * @param array $data
     *
     * @return Chat
     *
     * @throws Exception
     */
    public function openChat(User $user, array $data): Chat
    {
        DB::beginTransaction();
        try {
            $type = ChatTypeEnum::from($data['type']);
            $chattableId = $this->getChattableId($data);
            $chattableType = $this->getModelClassForType($type);

            // Check if chat already exists
            $existingChat = Chat::where('type', $type->value)
                ->where('chattable_type', $chattableType)
                ->where('chattable_id', $chattableId)
                ->whereHas('participants', function (Builder $query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->first();

            if ($existingChat) {
                DB::commit();
                return $existingChat->load(['participants', 'chattable', 'messages.sender', 'messages.attachments']);
            }

            // Create new chat
            $chat = Chat::create([
                'title' => $data['title'] ?? $this->generateChatTitle($type, $chattableId),
                'type' => $type->value,
                'created_by' => $user->id,
                'chattable_type' => $chattableType,
                'chattable_id' => $chattableId,
            ]);

            // Add participants based on chat type
            $this->addParticipantsBasedOnType($chat, $user, $type, $chattableId);

            DB::commit();

            return $chat->load(['participants', 'chattable', 'messages.sender', 'messages.attachments']);

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));
            throw $exception;
        }
    }

    /**
     * Get model class for chat type
     *
     * @param ChatTypeEnum $type
     *
     * @return string
     */
    private function getModelClassForType(ChatTypeEnum $type): string
    {
        return match($type) {
            ChatTypeEnum::USER => User::class,
            ChatTypeEnum::TEAM => Team::class,
            ChatTypeEnum::SERVICE => Service::class,
            ChatTypeEnum::JOB => Job::class,
            ChatTypeEnum::PROJECT => Project::class,
        };
    }

    /**
     * Generate chat title based on type and chattable
     *
     * @param ChatTypeEnum $type
     * @param int $chattableId
     *
     * @return string
     */
    private function generateChatTitle(ChatTypeEnum $type, int $chattableId): string
    {
        return match($type) {
            ChatTypeEnum::USER => 'Direct Message',
            ChatTypeEnum::TEAM => Team::find($chattableId)?->name . ' Chat' ?? 'Team Chat',
            ChatTypeEnum::SERVICE => 'Service Discussion: ' . (Service::find($chattableId)?->title_en ?? Service::find($chattableId)?->title_ar ?? 'Service'),
            ChatTypeEnum::JOB => 'Job Discussion: ' . (Job::find($chattableId)?->title ?? 'Part-time Job'),
            ChatTypeEnum::PROJECT => 'Project Discussion: ' . (Project::find($chattableId)?->title ?? 'Project'),
        };
    }

    /**
     * Get chattable ID based on type
     *
     * @param array $data
     *
     * @return int
     *
     * @throws Exception
     */
    private function getChattableId(array $data): int
    {
        return match($data['type']) {
            'user' => User::where('uuid', $data['user_uuid'])->firstOrFail()->id,
            'team' => Team::where('uuid', $data['team_uuid'])->firstOrFail()->id,
            'service' => Service::where('uuid', $data['service_uuid'])->firstOrFail()->id,
            'part_time_job' => Job::where('uuid', $data['part_time_job_uuid'])->firstOrFail()->id,
            'project' => Project::where('uuid', $data['project_uuid'])->firstOrFail()->id,
            default => throw new Exception(__('Invalid chat type')),
        };
    }

    /**
     * Add participants based on chat type
     *
     * @param Chat $chat
     * @param User $user
     * @param ChatTypeEnum $type
     * @param int $chattableId
     *
     * @return void
     */
    private function addParticipantsBasedOnType(Chat $chat, User $user, ChatTypeEnum $type, int $chattableId): void
    {
        $participants = [];

        switch ($type) {
            case ChatTypeEnum::USER:
                $targetUser = User::findOrFail($chattableId);
                $participants = [
                    $user->id => ['is_admin' => false, 'joined_at' => now()],
                    $targetUser->id => ['is_admin' => false, 'joined_at' => now()],
                ];
                break;

            case ChatTypeEnum::TEAM:
                $team = Team::with('members')->findOrFail($chattableId);
                foreach ($team->members as $member) {
                    $participants[$member->id] = [
                        'is_admin' => $member->pivot->role === 'admin' || $team->owner_id === $member->id,
                        'joined_at' => now(),
                    ];
                }
                break;

            case ChatTypeEnum::SERVICE:
                $service = Service::findOrFail($chattableId);
                $participants = [
                    $user->id => ['is_admin' => false, 'joined_at' => now()],
                ];
                if ($service->user_id !== $user->id) {
                    $participants[$service->user_id] = ['is_admin' => true, 'joined_at' => now()];
                }
                break;

            case ChatTypeEnum::JOB:
                $partTimeJob = Job::findOrFail($chattableId);
                $participants = [
                    $user->id => ['is_admin' => false, 'joined_at' => now()],
                ];
                if ($partTimeJob->user_id !== $user->id) {
                    $participants[$partTimeJob->user_id] = ['is_admin' => true, 'joined_at' => now()];
                }
                break;

            case ChatTypeEnum::PROJECT:
                $project = Project::findOrFail($chattableId);
                $participants = [
                    $user->id => ['is_admin' => false, 'joined_at' => now()],
                ];
                if ($project->user_id !== $user->id) {
                    $participants[$project->user_id] = ['is_admin' => true, 'joined_at' => now()];
                }
                break;
        }

        if (!empty($participants)) {
            $chat->participants()->attach($participants);
        }
    }

    /**
     * Update chat
     *
     * @param Chat $chat
     * @param User $user
     * @param array $data
     *
     * @return Chat
     *
     * @throws Exception
     */
    public function update(Chat $chat, User $user, array $data): Chat
    {
        DB::beginTransaction();
        try {
            // Check if user can update this chat
            if (!$this->canUserManageChat($chat, $user)) {
                throw new Exception(__('You do not have permission to update this chat'));
            }

            $chat->update([
                'title' => $data['title'] ?? $chat->title,
            ]);

            DB::commit();

            return $chat->load(['participants', 'chattable']);

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));
            throw $exception;
        }
    }

    /**
     * Add participants to chat
     *
     * @param Chat $chat
     * @param User $user
     * @param array $participantUuids
     *
     * @return Chat
     *
     * @throws Exception
     */
    public function addParticipants(Chat $chat, User $user, array $participantUuids): Chat
    {
        DB::beginTransaction();
        try {
            if (!$this->canUserManageChat($chat, $user)) {
                throw new Exception(__('You do not have permission to add participants'));
            }

            $users = User::whereIn('uuid', $participantUuids)->get();

            foreach ($users as $participant) {
                if (!$chat->participants()->where('user_id', $participant->id)->exists()) {
                    $chat->participants()->attach($participant->id, [
                        'is_admin' => false,
                        'joined_at' => now(),
                    ]);
                }
            }

            DB::commit();

            return $chat->load(['participants', 'chattable']);

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));
            throw $exception;
        }
    }

    /**
     * Remove participants from chat
     *
     * @param Chat $chat
     * @param User $user
     * @param array $participantUuids
     *
     * @return Chat
     *
     * @throws Exception
     */
    public function removeParticipants(Chat $chat, User $user, array $participantUuids): Chat
    {
        DB::beginTransaction();
        try {
            if (!$this->canUserManageChat($chat, $user)) {
                throw new Exception(__('You do not have permission to remove participants'));
            }

            $users = User::whereIn('uuid', $participantUuids)->get();
            $userIds = $users->pluck('id')->toArray();

            $chat->participants()->detach($userIds);

            DB::commit();

            return $chat->load(['participants', 'chattable']);

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));
            throw $exception;
        }
    }

    /**
     * Mark chat as read for user
     *
     * @param Chat $chat
     * @param User $user
     *
     * @return bool
     *
     * @throws Exception
     */
    public function markAsRead(Chat $chat, User $user): bool
    {
        DB::beginTransaction();
        try {
            if (!$this->canUserAccess($chat, $user)) {
                throw new Exception(__('You do not have access to this chat'));
            }

            $chat->participants()
                ->where('user_id', $user->id)
                ->update(['last_read_at' => now()]);

            DB::commit();

            return true;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));
            throw $exception;
        }
    }

    /**
     * Check if user can access chat
     *
     * @param Chat $chat
     * @param User $user
     *
     * @return bool
     */
    private function canUserAccess(Chat $chat, User $user): bool
    {
        return $chat->participants()->where('user_id', $user->id)->exists();
    }

    /**
     * Check if user can manage chat
     *
     * @param Chat $chat
     * @param User $user
     *
     * @return bool
     */
    private function canUserManageChat(Chat $chat, User $user): bool
    {
        $chatType = ChatTypeEnum::from($chat->type);

        return match($chatType) {
            ChatTypeEnum::USER => $chat->created_by === $user->id,
            ChatTypeEnum::TEAM => $chat->chattable?->owner_id === $user->id ||
                $chat->chattable?->members()->where('user_id', $user->id)->wherePivot('role', 'admin')->exists(),
            ChatTypeEnum::SERVICE => $chat->chattable?->user_id === $user->id || $chat->created_by === $user->id,
            ChatTypeEnum::JOB => $chat->chattable?->user_id === $user->id || $chat->created_by === $user->id,
            ChatTypeEnum::PROJECT => $chat->chattable?->user_id === $user->id || $chat->created_by === $user->id,
        };
    }
}
