<?php

namespace App\Http\Controllers\Api\PrivacyPolicy;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\JsonResponse;

/**
 * A class defines the privacy policy controller
 */
class PrivacyPolicyController extends BaseApiController
{
    /**
     * Get User Information
     *
     * @param User $user
     *
     * @return JsonResponse
     */
    public function privacyPolicy(User $user): JsonResponse
    {
        return $this->jsonSuccess(Setting::privacyPolicy());
    }
}
