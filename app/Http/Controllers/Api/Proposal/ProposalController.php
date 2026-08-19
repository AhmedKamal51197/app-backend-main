<?php

namespace App\Http\Controllers\Api\Proposal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Proposal\StoreProposalRequest;
use App\Http\Resources\Api\Proposal\ProposalResource;
use App\Models\Proposal;
use App\Models\Setting;
use App\Services\Proposal\ProposalService;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * A class defines the Proposal controller actions
 */
class ProposalController extends Controller
{
    use ApiResponse;

    /**
     * Load the service
     *
     * @param ProposalService $service
     */
    public function __construct(protected ProposalService $service)
    {
    }

    /**
     * Get user Proposals
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $proposals = $this->service->index(request()->user(), $request->input('limit', Setting::PAGE_RESULT_LIMIT));

        return $this->jsonSuccess(ProposalResource::collection($proposals));
    }

    /**
     * Store Proposal data
     *
     * @param StoreProposalRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function store(StoreProposalRequest $request): JsonResponse
    {
        $proposal = $this->service->store(request()->user(), $request->validated());

        return $this->jsonSuccess(
            ProposalResource::make($proposal),
            __('Proposal created successfully')
        );
    }

    /**
     * Show Proposal
     *
     * @param Proposal $proposal
     *
     * @return JsonResponse
     */
    public function show(Proposal $proposal): JsonResponse
    {
        $proposal->load(['attachments']);

        return $this->jsonSuccess(
            ProposalResource::make($proposal)
        );
    }
}
