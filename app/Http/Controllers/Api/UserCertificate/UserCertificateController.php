<?php

namespace App\Http\Controllers\Api\UserCertificate;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Http\Requests\Api\UserCertificate\EditUserCertificateRequest;
use App\Http\Requests\Api\UserCertificate\StoreUserCertificateRequest;
use App\Http\Resources\Api\UserCertificate\UserCertificateResource;
use App\Models\Certificate;
use App\Services\UserCertificate\UserCertificateService;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * A class defined for the user certificate controller
 */
class UserCertificateController extends BaseAdminController
{
    /**
     * Load the service
     *
     * @param UserCertificateService $service
     */
    public function __construct(protected UserCertificateService $service)
    {
    }

    /**
     * Index store user certificate
     *
     * @param StoreUserCertificateRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function storeUserCertificate(StoreUserCertificateRequest $request): JsonResponse
    {
        $user = $this->service->storeUserCertificate(request()->user(), $request->validated());

        return $this->jsonSuccess(UserCertificateResource::make($user));
    }

    /**
     * Update user certificate
     *
     * @param EditUserCertificateRequest $request
     * @param Certificate $certificate
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function editUserCertificate(Certificate $certificate, EditUserCertificateRequest $request, ): JsonResponse
    {
        $certificateUser = $this->service->updateUserCertificate(request()->user(), $certificate, $request->validated());

        return $this->jsonSuccess(UserCertificateResource::make($certificateUser));
    }

    /**
     * Remove user certificate
     *
     * @param Certificate $certificate
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function removeUserCertificate(Certificate $certificate): JsonResponse
    {
        $this->service->removeUserCertificate(request()->user(), $certificate);

        return $this->jsonSuccess(__('Certificate deleted successfully'));
    }
}
