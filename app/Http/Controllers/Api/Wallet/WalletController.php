<?php

namespace App\Http\Controllers\Api\Wallet;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\Api\Wallet\WalletResource;
use App\Models\Setting;
use App\Services\Wallet\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * A class defines the MyFatooraController controller
 */
class WalletController extends BaseApiController
{
    /**
     * The Location service
     *
     * @var WalletService
     */
    protected WalletService $walletService;

    /**
     * Call the service
     *
     * @param WalletService $walletService
     */
    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }
    /**
     * List of the system Customer
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = request()->user();


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
     * Get the authenticated user's wallet balance
     *
     * @return JsonResponse
     */
    public function getbalance(): JsonResponse
    {
        $walletBalance = auth()->user()->walletBalance();

        return $this->jsonSuccess($walletBalance);
    }
}
