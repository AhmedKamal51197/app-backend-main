<?php

namespace App\Http\Controllers\Admin\Project;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Http\Resources\Admin\Project\ProjectResource;
use App\Models\Project;
use App\Models\Setting;
use App\Services\Project\AdminProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * A class defines the Project controller
 */
class ProjectController extends BaseAdminController
{
    /**
     * Call the service
     *
     * @param AdminProjectService $service
     */
    public function __construct(protected AdminProjectService $service)
    {
    }

    /**
     * List of the system projects
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $result = $this->service->index(
            $request->input('search', ''),
            $request->input('limit', Setting::PAGE_RESULT_LIMIT),
            $request->input('page', Setting::PAGE),
            $request->input('status', ''),
            $request->input('order_by', 'latest'),
            $request->input('projects_categories_rate', 'monthly'),
        );

        $response = $this->jsonSuccess(ProjectResource::collection($result['projects'] ?? []));
        $responseData = json_decode($response->getContent(), true);

        $responseData['analytics'] = $result['analytics'] ?? [];
        $responseData['meta'] = $result['meta'] ?? [];

        return response()->json($responseData);
    }

    /**
     * Show project
     *
     * @param Project $project
     *
     * @return JsonResponse
     */
    public function show(Project $project): JsonResponse
    {
        $project = $this->service->show($project);

        return $this->jsonSuccess( ProjectResource::make($project));
    }

    /**
     * Approve cancellation request
     *
     * @param Project $project
     *
     * @return JsonResponse
     */
    public function approveCancellation(Project $project): JsonResponse
    {
        $project = $this->service->approveCancellation($project);

        return $this->jsonSuccess(ProjectResource::make($project), __('Cancellation approved successfully'));
    }

    /**
     * Reject cancellation request
     *
     * @param Project $project
     *
     * @return JsonResponse
     */
    public function rejectCancellation(Project $project): JsonResponse
    {
        $project = $this->service->rejectCancellation($project);

        return $this->jsonSuccess(ProjectResource::make($project), __('Cancellation rejected successfully'));
    }
}
