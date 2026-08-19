<?php

namespace App\Http\Controllers\Api\Kyc;

use App\Enums\KycStatusEnum;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\Kyc\StoreKycRequest;
use App\Http\Resources\Api\Kyc\KycResource;
use App\Models\Kyc;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Services\Kyc\KycService;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * A class defines the Kyc controller
 *
 * Store KYC
 */
class KycController extends BaseApiController
{
    /**
     * Load KYC service
     *
     * @param KycService $service
     */
    public function __construct(protected KycService $service)
    {
    }

    /**
     * List of the user KYC
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $kycs = $this->service->userKycs(request()->user(), $request->input('limit', Setting::PAGE_RESULT_LIMIT));

        return $this->jsonSuccess(KycResource::collection($kycs));
    }

    /**
     * Show the KYC
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
     * Store KYC data
     *
     * @param StoreKycRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function store(StoreKycRequest $request): JsonResponse
    {
        $user = request()->user();

        if (Kyc::whereUserId($user->id)->whereStatus(KycStatusEnum::PENDING->value)->exists()) {
            throw new Exception(__('You already have KYC pending request'));
        }

        $kyc = $this->service->store($user, $request->validated());

        return $this->jsonSuccess(
            KycResource::make($kyc),
            __('KYC created successfully')
        );
    }
}
