<?php

namespace App\Http\Controllers\Admin\Skill;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Http\Requests\Admin\Skill\EditSkillRequest;
use App\Http\Requests\Admin\Skill\StoreSkillRequest;
use App\Http\Resources\Admin\Skill\SkillResource;
use App\Models\Category;
use App\Models\Setting;
use App\Models\Skill;
use App\Services\Skill\SkillService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * A class defines the skill controller for admin
 */
class SkillController extends BaseAdminController
{
    /**
     * Call the service
     *
     * @param SkillService $service
     */
    public function __construct(protected SkillService $service)
    {
    }

    /**
     * List of skill
     *
     * @param Category $category
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Category $category, Request $request): JsonResponse
    {
        $skills = $this->service->index($category, $request->input('limit', Setting::PAGE_RESULT_LIMIT));

        return $this->jsonSuccess(SkillResource::collection($skills));
    }

    /**
     * Show skill
     *
     * @param Skill $skill
     *
     * @return JsonResponse
     */
    public function show(Skill $skill): JsonResponse
    {
        $skill->load(['category']);

        error_log($skill);


        return $this->jsonSuccess(SkillResource::make($skill));
    }

    /**
     * Store skill
     *
     * @param Category $category
     * @param StoreSkillRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function store(Category $category, StoreSkillRequest $request): JsonResponse
    {
        $skill = $this->service->store($category, $request->validated());

        return $this->jsonSuccess(
            SkillResource::make($skill),
            __('Skill created successfully')
        );
    }

    /**
     * Update skill
     *
     * @param EditSkillRequest $request
     * @param Skill $skill
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function edit(EditSkillRequest $request, Skill $skill): JsonResponse
    {
        $skill = $this->service->update($skill, $request->validated());

        return $this->jsonSuccess(
            SkillResource::make($skill),
            __('Skill updated successfully')
        );
    }

    /**
     * Delete skill
     *
     * @param Skill $skill
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function delete(Skill $skill): JsonResponse
    {
        $this->service->delete($skill);

        return $this->jsonSuccess([], __('Skill deleted successfully'));
    }
}
