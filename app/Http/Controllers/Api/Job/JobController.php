<?php

namespace App\Http\Controllers\Api\Job;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\Job\EditJobRequest;
use App\Http\Requests\Api\Job\StoreJobRequest;
use App\Http\Requests\Api\Service\AddAttachmentRequest;
use App\Http\Requests\Api\Service\EditServiceRequest;
use App\Http\Requests\Api\Service\ManageSkillsRequest;
use App\Http\Requests\Api\Service\StoreServiceRequest;
use App\Http\Resources\Api\Job\JobResource;
use App\Models\Attachment;
use App\Models\Job;
use App\Models\Setting;
use App\Services\Job\JobService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * A class defines the job controller
 */
class JobController extends BaseApiController
{
    /**
     * Load the service
     *
     * @param JobService $service
     */
    public function __construct(protected JobService $service)
    {
    }

    /**
     * List of the user jobs
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $jobs = $this->service->index(request()->user(), $request->input('limit', Setting::PAGE_RESULT_LIMIT));

        return $this->jsonSuccess(JobResource::collection($jobs));
    }

    /**
     * Show the job
     *
     * @param Job $job
     *
     * @return JsonResponse
     */
    public function show(Job $job): JsonResponse
    {
        $job->load(['attachments', 'category', 'skills', 'skills.skill']);

        return $this->jsonSuccess(JobResource::make($job));
    }

    /**
     * Store job data
     *
     * @param StoreJobRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function store(StoreJobRequest $request): JsonResponse
    {
        $job = $this->service->store(request()->user(), $request->validated());

        return $this->jsonSuccess(
            JobResource::make($job),
            __('Job created successfully')
        );
    }

    /**
     * Edit job data
     *
     * @param Job $job
     * @param EditJobRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function edit(Job $job, EditJobRequest $request): JsonResponse
    {
        $job = $this->service->edit($job, $request->validated());

        return $this->jsonSuccess(
            JobResource::make($job),
            __('Job updated successfully')
        );
    }

    /**
     * Delete the job
     *
     * @param Job $job
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function delete(Job $job): JsonResponse
    {
        $this->service->delete($job);

        return $this->jsonSuccess(__('Job deleted successfully'));
    }

    /**
     * Add attachment to job
     *
     * @param Job $job
     * @param AddAttachmentRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function addAttachment(Job $job, AddAttachmentRequest $request): JsonResponse
    {
        $job = $this->service->addAttachment(request()->user(), $job, $request->validated());

        return $this->jsonSuccess(
            JobResource::make($job),
            __('Attachment added successfully')
        );
    }

    /**
     * Delete job attachment
     *
     * @param Job $job
     * @param Attachment $attachment
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function deleteAttachment(Job $job, Attachment $attachment): JsonResponse
    {
        $job = $this->service->deleteAttachment($job, $attachment);

        return $this->jsonSuccess(
            JobResource::make($job),
            __('Attachment deleted successfully')
        );
    }

    /**
     * Add skills to job
     *
     * @param Job $job
     * @param ManageSkillsRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function addSkills(Job $job, ManageSkillsRequest $request): JsonResponse
    {
        $job = $this->service->storeJobSkills($job, $request->validated()['skill_ids']);

        return $this->jsonSuccess(
            JobResource::make($job),
            __('Skills added successfully')
        );
    }

    /**
     * Remove skills from job
     *
     * @param Job $job
     * @param ManageSkillsRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function removeSkills(Job $job, ManageSkillsRequest $request): JsonResponse
    {
        $job = $this->service->removeJobSkills($job, $request->validated()['skill_ids']);

        return $this->jsonSuccess(
            JobResource::make($job),
            __('Skills removed successfully')
        );
    }
}
