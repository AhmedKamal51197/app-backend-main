<?php

namespace App\Http\Controllers\Api\Service;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\Service\AddAttachmentRequest;
use App\Http\Requests\Api\Service\EditServicePackageRequest;
use App\Http\Requests\Api\Service\EditServiceRequest;
use App\Http\Requests\Api\Service\ManageSkillsRequest;
use App\Http\Requests\Api\Service\StoreServiceRequest;
use App\Http\Resources\Api\Service\ServiceResource;
use App\Models\Attachment;
use App\Models\Service;
use App\Models\ServicePackage;
use App\Models\Setting;
use App\Services\Service\ServiceService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * A class defines the service controller
 */
class ServiceController extends BaseApiController
{
    /**
     * Load the service
     *
     * @param ServiceService $service
     */
    public function __construct(protected ServiceService $service)
    {
    }

    /**
     * Browse all available services
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $services = $this->service->index(
            $request->input('search', ''),
            $request->input('limit', Setting::PAGE_RESULT_LIMIT),
            $request->input('page', Setting::PAGE),
            $request->input('category_id', ''),
            $request->input('user_id', ''),
            false
        );

        return $this->jsonSuccess(ServiceResource::collection($services));
    }

    /**
     * List of the services matching the current user's interests
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function indexInterests(Request $request): JsonResponse
    {
        $services = $this->service->indexByUserInterests(
            $request->user(),
            $request->input('limit', Setting::PAGE_RESULT_LIMIT),
            $request->input('page', Setting::PAGE)
        );

        return $this->jsonSuccess(ServiceResource::collection($services));
    }

    /**
     * List of the user services
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function indexUserServices(Request $request): JsonResponse
    {
        $services = $this->service->index(
            $request->input('search', ''),
            $request->input('limit', Setting::PAGE_RESULT_LIMIT),
            $request->input('page', Setting::PAGE),
            $request->input('category_id', ''),
            (string) $request->user()->id,
            true
        );

        return $this->jsonSuccess(ServiceResource::collection($services));
    }

    /**
     * Show the service
     *
     * @param Service $service
     *
     * @return JsonResponse
     */
    public function show(Service $service): JsonResponse
    {
        if (!$service->is_approved) {
            abort(404);
        }

        $service->load(['attachments', 'category', 'skills', 'skills.skill', 'packages', 'subCategory']);

        return $this->jsonSuccess(ServiceResource::make($service));
    }

    /**
     * Store service data
     *
     * @param StoreServiceRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function store(StoreServiceRequest $request): JsonResponse
    {
        $service = $this->service->store(request()->user(), $request->validated());

        return $this->jsonSuccess(
            ServiceResource::make($service),
            __('Service created successfully')
        );
    }

    /**
     * Edit a single service package
     *
     * @param Service $service
     * @param ServicePackage $package
     * @param EditServicePackageRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function editPackage(Service $service, ServicePackage $package, EditServicePackageRequest $request): JsonResponse
    {
        // Verify the package belongs to this service
        if ($package->service_id !== $service->id) {
            return $this->jsonError(__('Package does not belong to this service'), 403);
        }

        $service = $this->service->editServicePackage($service, $package, $request->validated());

        return $this->jsonSuccess(
            ServiceResource::make($service),
            __('Service package updated successfully')
        );
    }

    /**
     * Edit service data
     *
     * @param Service $service
     * @param EditServiceRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function edit(Service $service, EditServiceRequest $request): JsonResponse
    {
        $service = $this->service->edit($service, $request->validated());

        return $this->jsonSuccess(
            ServiceResource::make($service),
            __('Service updated successfully')
        );
    }

    /**
     * Delete the service
     *
     * @param Service $service
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function delete(Service $service): JsonResponse
    {
        $this->service->delete($service);

        return $this->jsonSuccess(__('Service deleted successfully'));
    }

    /**
     * Add attachment to service
     *
     * @param Service $service
     * @param AddAttachmentRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function addAttachment(Service $service, AddAttachmentRequest $request): JsonResponse
    {
        $service = $this->service->addAttachment(request()->user(), $service, $request->validated());

        return $this->jsonSuccess(
            ServiceResource::make($service),
            __('Attachment added successfully')
        );
    }

    /**
     * Delete service attachment
     *
     * @param Service $service
     * @param Attachment $attachment
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function deleteAttachment(Service $service, Attachment $attachment): JsonResponse
    {
        $service = $this->service->deleteAttachment($service, $attachment);

        return $this->jsonSuccess(
            ServiceResource::make($service),
            __('Attachment deleted successfully')
        );
    }

    /**
     * Add skills to service
     *
     * @param Service $service
     * @param ManageSkillsRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function addSkills(Service $service, ManageSkillsRequest $request): JsonResponse
    {
        $service = $this->service->storeServiceSkills($service, $request->validated()['skill_ids']);

        return $this->jsonSuccess(
            ServiceResource::make($service),
            __('Skills added successfully')
        );
    }

    /**
     * Remove skills from service
     *
     * @param Service $service
     * @param ManageSkillsRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function removeSkills(Service $service, ManageSkillsRequest $request): JsonResponse
    {
        $service = $this->service->removeServiceSkills($service, $request->validated()['skill_ids']);

        return $this->jsonSuccess(
            ServiceResource::make($service),
            __('Skills removed successfully')
        );
    }
}
