<?php

namespace App\Http\Controllers\Admin\Kyc;

use App\Enums\KycStatusEnum;
use App\Http\Controllers\Admin\BaseAdminController;
use App\Http\Resources\Admin\Kyc\KycResource;
use App\Models\Kyc;
use App\Models\Setting;
use App\Services\Kyc\KycService;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * A class defines the kyc controller
 */
class KycController extends BaseAdminController
{
    /**
     * Call the service
     *
     * @param KycService $service
     */
    public function __construct(protected KycService $service)
    {
    }

    /**
     * List of the system users
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return $this->jsonSuccess(KycResource::collection($this->service->index($request->input('limit', Setting::PAGE_RESULT_LIMIT))));
    }

    /**
     * List of pending KYCS
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function pending(Request $request): JsonResponse
    {
        return $this->jsonSuccess(KycResource::collection($this->service->indexByStatus(KycStatusEnum::PENDING->value, $request->input('limit', Setting::PAGE_RESULT_LIMIT))));
    }

    /**
     * List of approved KYCS
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function approved(Request $request): JsonResponse
    {
        return $this->jsonSuccess(KycResource::collection($this->service->indexByStatus(KycStatusEnum::APPROVED->value, $request->input('limit', Setting::PAGE_RESULT_LIMIT))));
    }

    /**
     * List of rejected KYCS
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function rejected(Request $request): JsonResponse
    {
        return $this->jsonSuccess(KycResource::collection($this->service->indexByStatus(KycStatusEnum::REJECTED->value, $request->input('limit', Setting::PAGE_RESULT_LIMIT))));
    }

    /**
     * Show KYC
     *
     * @param Kyc $kyc
     *
     * @return JsonResponse
     */
    public function show(Kyc $kyc): JsonResponse
    {
        $kyc->load(['attachments', 'user', 'country']);

        return $this->jsonSuccess(KycResource::make($kyc));
    }

    /**
     * Show KYC
     *
     * @param Kyc $kyc
     *
     * @return JsonResponse
     */
    public function delete(Kyc $kyc): JsonResponse
    {
        $kyc->delete();

        return $this->jsonSuccess([], __('KYC Deleted'));
    }

    /**
     * Approve KYC
     *
     * @param Kyc $kyc
     *
     * @return JsonResponse
     *
     * @throws AuthorizationException
     * @throws Exception
     */
    public function approve(Kyc $kyc): JsonResponse
    {
        $this->authorize('approve', $kyc);

        $this->service->accept($kyc);

        return $this->jsonSuccess([], __('KYC successfully approved'));
    }

    /**
     * Reject KYC
     *
     * @param Kyc $kyc
     *
     * @return JsonResponse
     *
     * @throws AuthorizationException
     * @throws Exception
     */
    public function reject(Kyc $kyc): JsonResponse
    {
        $this->authorize('reject', $kyc);

        $this->service->reject($kyc);

        return $this->jsonSuccess([], __('KYC successfully rejected'));
    }
}
