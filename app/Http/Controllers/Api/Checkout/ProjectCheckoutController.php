<?php

namespace App\Http\Controllers\Api\Checkout;

use App\Events\LogExceptionEvent;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\Checkout\ProjectCheckoutRequest;
use App\Models\Project;
use App\Services\Checkout\CheckoutPricingService;
use App\Services\Checkout\ProjectCheckoutService;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * A class defines the Proposal checkout controller
 */
class ProjectCheckoutController extends BaseApiController
{
    /**
     * Call the service
     *
     * @param ProjectCheckoutService $service
     */
    public function __construct(protected ProjectCheckoutService $service)
    {
    }

    /**
     * Create checkout for accepted proposal
     *
     * @param ProjectCheckoutRequest $request
     *
     * @return JsonResponse
     */
    public function checkout(ProjectCheckoutRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $project = Project::where('uuid', $data['project_id'])->firstOrFail();

            $checkout = $this->service->checkout($project, request()->user(), $request->validated());

            return $this->jsonSuccess($checkout, __('Checkout created successfully'));

        } catch (Exception $exception) {
            event(new LogExceptionEvent($exception));

            return $this->jsonError(__('Error while creating checkout, please try again later'));
        }
    }

    /**
     * Invoice breakdown for the accepted proposal, shown before payment.
     *
     * @param ProjectCheckoutRequest $request
     *
     * @return JsonResponse
     */
    public function preview(ProjectCheckoutRequest $request): JsonResponse
    {
        $project = Project::where('uuid', $request->validated()['project_id'])->firstOrFail();

        $price = (float) ($project->selectedProposal?->price ?? 0);

        return $this->jsonSuccess((new CheckoutPricingService())->breakdown($price));
    }
}
