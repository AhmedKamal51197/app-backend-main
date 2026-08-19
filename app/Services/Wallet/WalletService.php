<?php

namespace App\Services\Wallet;

use App\Events\LogExceptionEvent;
use App\Http\Resources\Api\Wallet\WalletResource;
use App\Models\PaymentGateway\PaymentGatewayCheckout;
use App\Models\Setting;
use App\Models\User;
use App\Models\Wallet;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;


/**
 * A class defines the Customer service
 */
class WalletService
{
    /**
     * Index the Wallets with monthly summary
     *
     * @param int $perPage
     * @return JsonResponse
     */
    public function index(int $perPage = Setting::PAGE_RESULT_LIMIT): JsonResponse
    {
        $user = auth()->user();
        // get the authenticated user's wallets
        $wallets = $user->wallets()->paginate($perPage);
        // Calculate monthly income and expenses
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        // Calculate income and expenses for the current month
        $income = $user->wallets()
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('credit');
        // Calculate expenses for the current month
        $expenses = $user->wallets()
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('debit');
        // Calculate total balance
        $totalBalance = $user->wallets()
            ->select(DB::raw('SUM(credit - debit) as balance'))
            ->value('balance');

        return response()->json([
            'wallets' => WalletResource::collection($wallets),
            'summary' => [
                'month_income' => $income,
                'month_expenses' => $expenses,
                'balance' => $totalBalance,
            ]
        ]);
    }

    /**
     * Create wallet
     *
     * @param User $user
     * @param float $debit
     * @param float $credit
     * @param string $title
     * @param Model $reference
     *
     * @return mixed
     */
    public static function createWallet(User $user, float $debit, float $credit, string $title, Model $reference): mixed
    {
        // Create a new wallet record
        return Wallet::create(
            [
                'user_id' => $user->getAttribute('id'),
                'debit' => $debit,
                'credit' => $credit,
                'title_ar' => $title,
                'title_en' => $title,
                'referencable_id' => $reference->id,
                'referencable_type' => get_class($reference),
            ]
        );
    }

    /**
     * Get the authenticated user's wallet Summary
     *
     * @param User $user
     *
     * @return array
     */
    function getSummary(User $user): array
    {
        /**
         * get the Summary data for the user
         */
        return [
            'summary' => [
                'pendingBalance' => $user->pendingBalance(),
                'active_orders' => $user->paymentGatewayCheckouts()->count(),
                'earnings' => $user->walletBalance()
            ]
        ];
    }
}
