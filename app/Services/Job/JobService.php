<?php

namespace App\Services\Job;

use App\Actions\Attachments\StoreAttachmentAction;
use App\Actions\Files\GuessFileTypeAction;
use App\Enums\AttachmentDocumentTypeEnum;
use App\Enums\AttachmentStorageEnum;
use App\Events\LogExceptionEvent;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\Job;
use App\Models\JobTag;
use App\Models\Setting;
use App\Models\Skill;
use App\Models\User;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * A class defines the job service
 */
class JobService
{
    /**
     * Get all jobs
     *
     * @param User $user
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public function index(User $user, int $perPage = Setting::PAGE_RESULT_LIMIT): LengthAwarePaginator
    {
        return Job::query()
            ->with(['attachments', 'category', 'skills', 'skills.skill'])
            ->where('user_id', '=', $user->getAttribute('id'))
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Store new job
     *
     * @param User $user
     * @param array $data
     *
     * @return Job
     *
     * @throws Exception
     */
    public function store(User $user, array $data): Job
    {
        $category = Category::where('uuid', '=', $data['category_id'])->firstOrFail();

        DB::beginTransaction();
        try {
            $job = Job::create([
                'role' => $data['role'],
                'description_ar' => $data['description_ar'],
                'description_en' => $data['description_en'],
                'category_id' => $category->getAttribute('id'),
                'weakly_salary' => $data['weakly_salary'],
                'monthly_salary' => $data['monthly_salary'],
                'working_hours' => $data['working_hours'],
                'user_id' => $user->getAttribute('id'),
            ]);

            if (isset($data['attachments'])) {
                StoreAttachmentAction::store($job, $data['attachments'], 'attachments', false);
            }

            self::storeJobSkills($job, $data['skill_ids'] ?? []);

            DB::commit();

            return $job->load(['attachments', 'category', 'skills', 'skills.skill']);

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Update existing job
     *
     * @param Job $job
     * @param array $data
     *
     * @return Job
     *
     * @throws Exception
     */
    public function edit(Job $job, array $data): Job
    {
        DB::beginTransaction();
        try {
            $updateData = [
                'role' => $data['role'] ?? $job->role,
                'description_ar' => $data['description_ar'] ?? $job->description_ar,
                'description_en' => $data['description_en'] ?? $job->description_en,
                'weakly_salary' => $data['weakly_salary'] ?? $job->weakly_salary,
                'monthly_salary' => $data['monthly_salary'] ?? $job->monthly_salary,
                'working_hours' => $data['revisions'] ?? $job->working_hours,
            ];

            $job->update($updateData);

            DB::commit();

            return $job->load(['attachments', 'category', 'skills', 'skills.skill']);

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Delete job
     *
     * @param Job $job
     *
     * @return bool
     *
     * @throws Exception
     */
    public function delete(Job $job): bool
    {
        DB::beginTransaction();
        try {
            if ($job->attachments->count() > 0) {
                foreach ($job->attachments as $attachment) {
                    Storage::disk($attachment->disk)->delete($attachment->path);
                }
            }
            $result = $job->delete();

            DB::commit();

            return $result;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Delete job attachment
     *
     * @param Job $job
     * @param Attachment $attachment
     *
     * @return Job
     *
     * @throws Exception
     */
    public function deleteAttachment(Job $job, Attachment $attachment): Job
    {
        DB::beginTransaction();
        try {
            // Verify the attachment belongs to this job
            $jobAttachment = $job->attachments()->where('id', $attachment->id)->first();

            if (!$jobAttachment) {
                throw new Exception(__('Job not found for this service'));
            }

            if (Storage::disk($jobAttachment->disk)->exists($jobAttachment->path)) {
                Storage::disk($jobAttachment->disk)->delete($jobAttachment->path);
            }

            $jobAttachment->delete();

            DB::commit();

            return $job->load(['attachments', 'category', 'skills', 'skills.skill']);

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Add an attachment to the job
     *
     * @param User $user
     * @param Job $job
     * @param array $data
     *
     * @return Job
     *
     * @throws Exception
     */
    public function addAttachment(User $user, Job $job, array $data): Job
    {
        DB::beginTransaction();
        try {
            StoreAttachmentAction::store($job, $data['attachment'], 'attachments', false);

            DB::commit();

            return $job->load(['attachments', 'category', 'skills', 'skills.skill']);

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Store job skills without detaching the old ones.
     *
     * @param Job $job
     * @param array $skills
     *
     * @return Job
     *
     * @throws Exception
     */
    public function storeJobSkills(Job $job, array $skills): Job
    {
        DB::beginTransaction();
        try {
            $skills = Skill::whereIn('uuid', $skills)->get();

            foreach ($skills as $skill) {
                $alreadyAttached = $job->skills()
                    ->where('skill_id', $skill->id)
                    ->exists();

                if (!$alreadyAttached) {
                    JobTag::create([
                        'skill_id' => $skill->id,
                        'job_id' => $job->getAttribute('id')
                    ]);
                }
            }

            DB::commit();

            return $job->load(['attachments', 'category', 'skills', 'skills.skill']);

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Remove job skills.
     *
     * @param Job $job
     * @param array $skills
     *
     * @return Job
     *
     * @throws Exception
     */
    public function removeJobSkills(Job $job, array $skills): Job
    {
        DB::beginTransaction();
        try {
            $skills = Skill::whereIn('uuid', $skills)->get();
            $skillIds = $skills->pluck('id')->toArray();

            $job->skills()
                ->whereIn('skill_id', $skillIds)
                ->delete();

            DB::commit();

            return $job->load(['attachments', 'category', 'skills', 'skills.skill']);

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }
}
