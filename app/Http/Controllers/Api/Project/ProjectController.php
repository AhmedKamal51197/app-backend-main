<?php

namespace App\Http\Controllers\Api\Project;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Project\CancelProjectRequest;
use App\Http\Requests\Api\Project\StoreProjectRequest;
use App\Http\Resources\Api\Project\ProjectResource;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\Setting;
use App\Services\Project\ProjectService;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * A class defines the Project controller actions
 */
class ProjectController extends Controller
{
    use ApiResponse;

    /**
     * Load the service
     *
     * @param ProjectService $service
     */
    public function __construct(protected ProjectService $service)
    {
    }

    /**
     * Get all projects
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $projects = $this->service->index(
            $request->input('search', ''),
            $request->input('limit', Setting::PAGE_RESULT_LIMIT),
            $request->input('page', Setting::PAGE),
            $request->input('status', ''),
            $request->input('user_id', ''),
            false
        );

        return $this->jsonSuccess(ProjectResource::collection($projects));
    }

    /**
     * Get user projects
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function indexUserProjects(Request $request): JsonResponse
    {
        $projects = $this->service->index(
            $request->input('search', ''),
            $request->input('limit', Setting::PAGE_RESULT_LIMIT),
            $request->input('page', Setting::PAGE),
            $request->input('status', ''),
            (string) $request->user()->id,
            true
        );

        return $this->jsonSuccess(ProjectResource::collection($projects));
    }

    /**
     * Store project data
     *
     * @param StoreProjectRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = $this->service->store(request()->user(), $request->validated());

        return $this->jsonSuccess(
            ProjectResource::make($project),
            __('Project created successfully')
        );
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
        $project->load(['attachments', 'category', 'subCategory',  'proposals', 'proposals.user', 'proposals.attachments', 'selectedProposal.attachments', 'rates', 'rates.user', 'rates.ratedUser']);

        return $this->jsonSuccess(
            ProjectResource::make($project)
        );
    }

    /**
     * Publish project
     *
     * @param Project $project
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function publish(Project $project): JsonResponse
    {
        $this->authorize('own', $project);
        $project = $this->service->publish($project);

        return $this->jsonSuccess(
            ProjectResource::make($project),
            __('Project published successfully')
        );
    }

    /**
     * Cancel project
     *
     * @param Project $project
     * @param CancelProjectRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function cancel(Project $project, CancelProjectRequest $request): JsonResponse
    {
        $this->authorize('own', $project);

        $project = $this->service->cancel($project, $request->validated());

        return $this->jsonSuccess(ProjectResource::make($project), __('Project cancelled successfully'));
    }

    /**
     * Complete project
     *
     * @param Project $project
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function complete(Project $project): JsonResponse
    {
        $this->authorize('own', $project);

        $project = $this->service->complete($project);

        return $this->jsonSuccess(ProjectResource::make($project), __('Project completed successfully'));
    }

    /**
     * Accept proposal for project
     * @param Project $project
     * @param Proposal $proposal
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function acceptProposal(Project $project, Proposal $proposal): JsonResponse
    {
        $this->authorize('own', $project);
        $project = $this->service->acceptProposal($project, $proposal);

        return $this->jsonSuccess(
            ProjectResource::make($project)
        );
    }
}
