<?php

namespace App\Http\Controllers\Admin\Wallet;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Http\Resources\Admin\PaymentRequest\PaymentRequestResource;
use App\Http\Resources\Admin\Receipt\ReceiptResource;
use App\Http\Resources\Admin\User\UserMiniResource;
use App\Http\Resources\Admin\Wallet\DepositResource;
use App\Models\Setting;
use App\Models\Wallet;
use App\Services\Wallet\AdminWalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * A class defines the admin wallet controller
 */
class WalletController extends BaseAdminController
{
    /**
     * Call the service
     *
     * @param AdminWalletService $service
     */
    public function __construct(protected AdminWalletService $service)
    {
    }

    /**
     * Get wallet dashboard with analytics, user wallets, and financial flow
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $data = [
            'user_wallets_limit' => $request->input('user_wallets_limit', Setting::PAGE_RESULT_LIMIT),
            'user_wallets_page' => $request->input('user_wallets_page', Setting::PAGE),
            'user_role' => $request->input('user_role', 'seeker'),
            'deposits_limit' => $request->input('deposits_limit', Setting::PAGE_RESULT_LIMIT),
            'deposits_page' => $request->input('deposits_page', Setting::PAGE),
            'withdrawals_limit' => $request->input('withdrawals_limit', Setting::PAGE_RESULT_LIMIT),
            'withdrawals_page' => $request->input('withdrawals_page', Setting::PAGE),
            'withdrawal_status' => $request->input('withdrawal_status', ''),
        ];

        $result = $this->service->index($data);

        return response()->json([
            'error' => false,
            'message' => '',
            'data' => [
                'analytics' => $result['analytics'],
                'user_wallets' => [
                    'data' => UserMiniResource::collection($result['user_wallets']),
                    'meta' => [
                        'total_pages' => $result['user_wallets']->lastPage(),
                        'current_page' => $result['user_wallets']->currentPage(),
                        'total_items' => $result['user_wallets']->total(),
                        'per_page' => $result['user_wallets']->perPage(),
                    ],
                ],
                'deposits' => [
                    'data' => DepositResource::collection($result['deposits']),
                    'meta' => [
                        'total_pages' => $result['deposits']->lastPage(),
                        'current_page' => $result['deposits']->currentPage(),
                        'total_items' => $result['deposits']->total(),
                        'per_page' => $result['deposits']->perPage(),
                    ],
                ],
                'withdrawals' => [
                    'data' => PaymentRequestResource::collection($result['withdrawals']),
                    'meta' => [
                        'total_pages' => $result['withdrawals']->lastPage(),
                        'current_page' => $result['withdrawals']->currentPage(),
                        'total_items' => $result['withdrawals']->total(),
                        'per_page' => $result['withdrawals']->perPage(),
                    ],
                ],
            ],
        ]);
    }

    /**
     * Get receipt for a specific wallet transaction
     *
     * @param Wallet $wallet
     *
     * @return JsonResponse
     */
    public function getReceipt(Wallet $wallet): JsonResponse
    {
        $receipt = $wallet->receipt;

        if (!$receipt) {
            return $this->jsonSuccess([]);
        }

        $receipt->load('user');

        return $this->jsonSuccess(ReceiptResource::make($receipt));
    }
}
