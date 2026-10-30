<?php

namespace App\Http\Controllers\Api\Checkout;

use App\Enums\ProjectStatusEnum;
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

            if ($error = $this->checkoutError($project)) {
                return $this->jsonError($error);
            }

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

        if ($error = $this->checkoutError($project)) {
            return $this->jsonError($error);
        }

        return $this->jsonSuccess((new CheckoutPricingService())->breakdown((float) $project->selectedProposal->price));
    }

    /**
     * Reason the current user can't pay for this project, or null when allowed.
     *
     * @param Project $project
     *
     * @return string|null
     */
    private function checkoutError(Project $project): ?string
    {
        if ((int) $project->user_id !== (int) request()->user()->id) {
            return __('This action is unauthorized.');
        }

        if (!$project->selectedProposal) {
            return __('Please accept a proposal before payment');
        }

        if ($project->status !== ProjectStatusEnum::PENDING->value) {
            return __('This project has already been paid or closed');
        }

        return null;
    }
}
