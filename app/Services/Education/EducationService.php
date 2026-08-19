<?php

namespace App\Services\Education;

use App\Actions\Attachments\StoreAttachmentAction;
use App\Actions\Files\GuessFileTypeAction;
use App\Enums\AttachmentDocumentTypeEnum;
use App\Enums\AttachmentStorageEnum;
use App\Events\LogExceptionEvent;
use App\Models\Education;
use App\Models\EducationDegree;
use App\Models\EducationMajor;
use App\Models\Setting;
use App\Models\User;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * A class defines the education service
 */
class EducationService
{
    /**
     * Get all educations
     *
     * @param User $user
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public function index(User $user, int $perPage = Setting::PAGE_RESULT_LIMIT): LengthAwarePaginator
    {
        return Education::query()
            ->with(['educationDegree', 'image'])
            ->where('user_id', '=', $user->getAttribute('id'))
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Store new education
     *
     * @param User $user
     * @param array $data
     *
     * @return Education
     *
     * @throws Exception
     */
    public function store(User $user, array $data): Education
    {
        DB::beginTransaction();
        try {
            // Find the degree and major by UUID

            $degree = EducationDegree::where('uuid', $data['education_degree_id'])->firstOrFail();

            $education = Education::create([
                'title' => $data['title'],
                'graduation_year' => $data['graduation_year'] ?? null,
                'description' => $data['description'] ?? null,
                'user_id' => $user->getAttribute('id'),
                'education_degree_id' => $degree->id,
                'major' => $data['major']
            ]);


            // Handle image upload if provided
            if (isset($data['image']) && $data['image']) {
                StoreAttachmentAction::store($education, $data['image'], 'image', false);
            }

            DB::commit();

            return $education->load(['educationDegree', 'image']);

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Update existing education
     *
     * @param Education $education
     * @param array $data
     *
     * @return Education
     *
     * @throws Exception
     */
    public function edit(Education $education, array $data): Education
    {
        DB::beginTransaction();
        try {
            // Update basic fields
            $updateData = [];

            if (isset($data['title'])) {
                $updateData['title'] = $data['title'];
            }

            if (array_key_exists('graduation_year', $data)) {
                $updateData['graduation_year'] = $data['graduation_year'];
            }

            if (array_key_exists('description', $data)) {
                $updateData['description'] = $data['description'];
            }

            // Update degree if provided
            if (isset($data['education_degree_uuid'])) {
                $degree = EducationDegree::where('uuid', $data['education_degree_id'])->firstOrFail();
                $updateData['education_degree_id'] = $degree->id;
            }

            // Update major if provided
            if (isset($data['major'])) {
                $updateData['major'] = $data['major'];
            }

            if (isset($data['image']) && $data['image']) {
                StoreAttachmentAction::store($education, $data['image'], 'image', true);
            }

            // Update the education record
            $education->update($updateData);

            DB::commit();

            return $education->load(['educationDegree', 'image']);

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Delete education
     *
     * @param Education $education
     *
     * @return bool
     *
     * @throws Exception
     */
    public function delete(Education $education): bool
    {
        DB::beginTransaction();
        try {
            $result = $education->delete();

            DB::commit();

            return $result;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Adding request to get the info for majors, degrees
     *
     * @return array
     */
    public function info(): array
    {
        $majors = EducationMajor::query()
            ->orderByDesc('created_at')->get();
        $degrees = EducationDegree::query()
            ->orderByDesc('created_at')->get();

        return [
            'majors' => $majors,
            'degrees' => $degrees,
        ];
    }
}
