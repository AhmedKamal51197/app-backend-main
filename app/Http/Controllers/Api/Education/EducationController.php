<?php

namespace App\Http\Controllers\Api\Education;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\Education\EditEducationRequest;
use App\Http\Requests\Api\Education\StoreEducationRequest;
use App\Http\Resources\Api\Education\EducationInfo;
use App\Http\Resources\Api\Education\EducationResource;
use App\Models\Education;
use App\Models\Setting;
use App\Services\Education\EducationService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * A class defines the education controller
 */
class EducationController extends BaseApiController
{
    /**
     * Load the service
     *
     * @param EducationService $service
     */
    public function __construct(protected EducationService $service)
    {
    }

    /**
     * List of the user educations
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $educations = $this->service->index(request()->user(), $request->input('limit', Setting::PAGE_RESULT_LIMIT));

        return $this->jsonSuccess(EducationResource::collection($educations));
    }

    /**
     * Show the education
     *
     * @param Education $education
     *
     * @return JsonResponse
     */
    public function show(Education $education): JsonResponse
    {
        $education->load(['educationMajor', 'educationDegree', 'image']);

        return $this->jsonSuccess(EducationResource::make($education));
    }

    /**
     * Store education data
     *
     * @param StoreEducationRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function store(StoreEducationRequest $request): JsonResponse
    {
        $education = $this->service->store(request()->user(), $request->validated());

        return $this->jsonSuccess(
            EducationResource::make($education),
            __('Education created successfully')
        );
    }

    /**
     * Edit education data
     *
     * @param Education $education
     * @param EditEducationRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function edit(Education $education, EditEducationRequest $request): JsonResponse
    {
        $education = $this->service->edit($education, $request->validated());

        return $this->jsonSuccess(
            EducationResource::make($education),
            __('Education updated successfully')
        );
    }

    /**
     * Delete the education
     *
     * @param Education $education
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function delete(Education $education): JsonResponse
    {
        $this->service->delete($education);

        return $this->jsonSuccess(__('Education deleted successfully'));
    }

    /**
     * Education info
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function info(Request $request): JsonResponse
    {
        $info = $this->service->info();

        return $this->jsonSuccess(
            EducationInfo::make($info),
        );
    }
}
