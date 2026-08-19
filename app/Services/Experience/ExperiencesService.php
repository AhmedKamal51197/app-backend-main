<?php

namespace App\Services\Experience;

use App\Actions\Attachments\StoreAttachmentAction;
use App\Actions\Files\GuessFileTypeAction;
use App\Enums\AttachmentDocumentTypeEnum;
use App\Enums\AttachmentStorageEnum;
use App\Events\LogExceptionEvent;
use App\Models\Experience;
use App\Models\Setting;
use App\Models\User;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * A class defines the experience service
 */
class ExperiencesService
{
    /**
     * Get all experiences
     *
     * @param User $user
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public function index(User $user, int $perPage = Setting::PAGE_RESULT_LIMIT): LengthAwarePaginator
    {
        return Experience::query()
            ->with(['image'])
            ->where('user_id', '=', $user->getAttribute('id'))
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Store new experience
     *
     * @param User $user
     * @param array $data
     *
     * @return Experience
     *
     * @throws Exception
     */
    public function store(User $user, array $data): Experience
    {
        DB::beginTransaction();
        try {
            $experience = Experience::create([
                'title' => $data['title'],
                'company' => $data['company'],
                'description' => $data['description'] ?? null,
                'employment_type' => $data['employment_type'],
                'start_date' => $data['start_date'] ?? null,
                'is_current' => $data['is_current'],
                'end_date' => $data['end_date'] ?? null,
                'user_id' => $user->getAttribute('id'),
            ]);

            // Handle image upload if provided
            if (isset($data['image']) && $data['image']) {
                StoreAttachmentAction::store($experience, $data['image'], 'image', false);
            }

            DB::commit();

            return $experience->load(['image']);

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Update existing experience
     *
     * @param Experience $experience
     * @param array $data
     *
     * @return Experience
     *
     * @throws Exception
     */
    public function edit(Experience $experience, array $data): Experience
    {
        DB::beginTransaction();
        try {
            $updateData = [
                'title' => $data['title'] ?? $experience->title,
                'company' => $data['company'] ?? $experience->company,
                'description' => $data['description'] ?? $experience->description,
                'employment_type' => $data['employment_type'] ?? $experience->employment_type,
                'start_date' => $data['start_date'] ?? $experience->start_date,
                'is_current' => $data['is_current'] ?? $experience->is_current,
                'end_date' => $data['end_date'] ?? $experience->end_date,
            ];

            // Update the experience
            $experience->update($updateData);

            // Handle image update
            if (isset($data['image']) && $data['image']) {
                StoreAttachmentAction::store($experience, $data['image'], 'image', true);
            }

            DB::commit();

            return $experience->load(['image']);

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Delete experience
     *
     * @param Experience $experience
     *
     * @return bool
     *
     * @throws Exception
     */
    public function delete(Experience $experience): bool
    {
        DB::beginTransaction();
        try {
            $result = $experience->delete();

            DB::commit();

            return $result;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }
}
