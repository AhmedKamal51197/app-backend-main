<?php

namespace App\Http\Controllers\Api\Provider;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\Api\Provider\ProviderResource;
use App\Models\User;
use App\Services\Provider\ProviderService;
use Illuminate\Http\JsonResponse;

/**
 * A class defines the provider controller
 */
class ProviderController extends BaseApiController
{
    /**
     * Load the service
     *
     * @param ProviderService $service
     */
    public function __construct(protected ProviderService $service)
    {
    }

    /**
     * Get User Information
     *
     * @param User $user
     *
     * @return JsonResponse
     */
    public function profile(User $user): JsonResponse
    {
        $user->load([
            'category',
            'subCategory',
            'country',
            'userSubCategories', 'userSubCategories.subCategory',
            'userSkills', 'userSkills.skill',
            'avatar',
            'bankAccount',
            'paypal',
            'rates',
            'rates.user',
            'rates.order',
            'rates.order.orderable',
            'rates.order.orderable.service',
            'educations', 'educations.educationDegree', 'educations.image',
            'portfolios', 'portfolios.attachments', 'portfolios.duration', 'portfolios.industry',
            'services', 'services.attachments', 'services.packages',
            'experiences', 'experiences.image',
            'userCertificates.file', 'userCertificates.certificate', 'userCertificates.certificate.provider', 'userCertificates',
        ]);

        return $this->jsonSuccess(ProviderResource::make($user));
    }
}
