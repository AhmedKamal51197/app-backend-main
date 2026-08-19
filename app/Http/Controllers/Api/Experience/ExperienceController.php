<?php

namespace App\Http\Controllers\Api\Experience;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\Experience\EditExperienceRequest;
use App\Http\Requests\Api\Experience\StoreExperienceRequest;
use App\Http\Resources\Api\Experience\ExperienceResource;
use App\Models\Experience;
use App\Models\Setting;
use App\Services\Experience\ExperiencesService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * A class defines the experience controller
 */
class ExperienceController extends BaseApiController
{
    /**
     * Load the service
     *
     * @param ExperiencesService $service
     */
    public function __construct(protected ExperiencesService $service)
    {
    }

    /**
     * List of the user experiences
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $experiences = $this->service->index(request()->user(), $request->input('limit', Setting::PAGE_RESULT_LIMIT));

        return $this->jsonSuccess(ExperienceResource::collection($experiences));
    }

    /**
     * Show the experience
     *
     * @param Experience $experience
     *
     * @return JsonResponse
     */
    public function show(Experience $experience): JsonResponse
    {
        $experience->load(['image']);

        return $this->jsonSuccess(ExperienceResource::make($experience));
    }

    /**
     * Store experience data
     *
     * @param StoreExperienceRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function store(StoreExperienceRequest $request): JsonResponse
    {
        $experience = $this->service->store(request()->user(), $request->validated());

        return $this->jsonSuccess(
            ExperienceResource::make($experience),
            __('Experience created successfully')
        );
    }

    /**
     * Edit experience data
     *
     * @param Experience $experience
     * @param EditExperienceRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function edit(Experience $experience, EditExperienceRequest $request): JsonResponse
    {
        $experience = $this->service->edit($experience, $request->validated());

        return $this->jsonSuccess(
            ExperienceResource::make($experience),
            __('Experience updated successfully')
        );
    }

    /**
     * Delete the experience
     *
     * @param Experience $experience
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function delete(Experience $experience): JsonResponse
    {
        $this->service->delete($experience);

        return $this->jsonSuccess(__('Experience deleted successfully'));
    }
}
