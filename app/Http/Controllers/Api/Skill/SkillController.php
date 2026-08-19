<?php

namespace App\Http\Controllers\Api\Skill;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\Api\Skill\SkillResource;
use App\Models\Category;
use App\Models\Setting;
use App\Models\Skill;
use App\Services\Skill\SkillService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * A class defines the skill controller for user API
 */
class SkillController extends BaseApiController
{
    /**
     * load the service
     *
     * @param SkillService $service
     */
    public function __construct(protected SkillService $service)
    {
    }

    /**
     * List all skills
     *
     * @param Category $category
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Category $category,Request $request): JsonResponse
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

        return $this->jsonSuccess(SkillResource::make($skill));
    }
}
