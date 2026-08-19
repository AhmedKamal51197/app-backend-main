<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Http\Resources\Admin\Kyc\KycResource;
use App\Http\Resources\Admin\PaymentRequest\PaymentRequestResource;
use App\Http\Resources\Admin\Report\ReportResource;
use App\Http\Resources\Admin\Users\UsersResource;
use App\Http\Resources\Api\Order\OrderResource;
use App\Http\Resources\Api\Service\ServiceResource;
use App\Http\Resources\Api\Wallet\WalletResource;
use App\Models\Setting;
use App\Models\User;
use App\Services\Kyc\KycService;
use App\Services\Order\OrderService;
use App\Services\PaymentRequest\PaymentRequestService;
use App\Services\Report\ReportService;
use App\Services\Service\ServiceService;
use App\Services\Users\UsersService;
use App\Services\Wallet\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * A class defines the user controller's actions
 */
class UserDetailsController extends BaseAdminController
{
    public function __construct(protected WalletService         $walletService,
                                protected OrderService          $orderService,
                                protected ReportService         $reportService,
                                protected KycService            $kycService,
                                protected ServiceService        $serviceService,
                                protected PaymentRequestService $paymentRequestService)
    {
    }

    /**
     * User details wallet
     *
     * @param User $user
     * @param Request $request
     * @return JsonResponse
     */
    public function wallet(User $user, Request $request): JsonResponse
    {

        $wallets = $user->wallets()->paginate($request->input('limit', Setting::PAGE_RESULT_LIMIT));
        $wallets->getCollection()->transform(function ($wallet) {
            return WalletResource::make($wallet);
        });

        return $this->jsonSuccess([
            'wallets' => $wallets,
            'summary' => $this->walletService->getSummary($user),
        ]);
    }

    /**
     * Get user payment requests
     *
     * @param User $user
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function paymentRequests(User $user, Request $request): JsonResponse
    {
        return $this->jsonSuccess(PaymentRequestResource::collection($this->paymentRequestService->userPaymentRequests($user, $request->input('limit', Setting::PAGE_RESULT_LIMIT))));
    }

    /**
     * Get User orders
     *
     * @param User $user
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function orders(User $user, Request $request): JsonResponse
    {
        return $this->jsonSuccess(OrderResource::collection($this->orderService->index($user, $request->input('limit', Setting::PAGE_RESULT_LIMIT))));
    }

    /**
     * Get User Reports
     *
     * @param User $user
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function reports(User $user, Request $request): JsonResponse
    {
        return $this->jsonSuccess(ReportResource::collection($this->reportService->userReports($user, $request->input('limit', Setting::PAGE_RESULT_LIMIT))));
    }

    /**
     * Get user KYCS
     *
     * @param User $user
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function kycs(User $user, Request $request): JsonResponse
    {
        return $this->jsonSuccess(KycResource::collection($this->kycService->userKycs($user, $request->input('limit', Setting::PAGE_RESULT_LIMIT))));
    }

    /**
     * Get user services
     *
     * @param User $user
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function services(User $user, Request $request): JsonResponse
    {
        return $this->jsonSuccess(ServiceResource::collection($this->serviceService->index($user, $request->input('limit', Setting::PAGE_RESULT_LIMIT))));
    }
}
