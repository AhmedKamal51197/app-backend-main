<?php

namespace App\Services\PaymentRequest;

use App\Enums\PaymentRequestStatusEnum;
use App\Events\LogExceptionEvent;
use App\Models\PaymentRequest;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\Payments\PaymentRequestAddedNotification;
use App\Notifications\Payments\PaymentRequestApprovedNotification;
use App\Notifications\Payments\PaymentRequestRejectedNotification;
use App\Notifications\Receipts\PaymentReceiptNotification;
use App\Notifications\Wallet\WalletCreditNotification;
use App\Notifications\Wallet\WalletDebitNotification;
use App\Services\Receipt\ReceiptService;
use App\Services\Wallet\WalletService;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * A class defines the Payment Request service
 */
class PaymentRequestService
{
    /**
     * Index the payment-requests
     *
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public function index(int $perPage = Setting::PAGE_RESULT_LIMIT): LengthAwarePaginator
    {
        return PaymentRequest::query()
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Index the payment-requests by status
     *
     * @param string $status
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public function indexByStatus(string $status, int $perPage = Setting::PAGE_RESULT_LIMIT): LengthAwarePaginator
    {
        return PaymentRequest::query()
            ->orderByDesc('created_at')
            ->where('status', '=', $status)
            ->paginate($perPage);
    }

    /**
     * Index the user payment requests
     *
     * @param User $user
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public static function userPaymentRequests(User $user, int $perPage = Setting::PAGE_RESULT_LIMIT): LengthAwarePaginator
    {
        return PaymentRequest::query()
            ->orderByDesc('created_at')
            ->where('user_id', '=', $user->getAttribute('id'))
            ->paginate($perPage);
    }

    /**
     * Accept the Payment Request
     *
     * @param PaymentRequest $paymentRequest
     *
     * @return PaymentRequest
     *
     * @throws Exception
     */
    public function accept(PaymentRequest $paymentRequest): PaymentRequest
    {
        DB::beginTransaction();
        try {
            $paymentRequest->update([
                'status' => PaymentRequestStatusEnum::APPROVED->value,
            ]);

            ReceiptService::createReceipt(
                $paymentRequest->user,
                $paymentRequest->amount,
                $paymentRequest
            );

            $paymentRequest->user->notify(new PaymentRequestApprovedNotification($paymentRequest->getAttribute('amount')));

            $paymentRequest->user->notify(new PaymentReceiptNotification(
                name: $paymentRequest->user->name,
                amount: $paymentRequest->amount,
                date: now()->format('Y-m-d H:i:s')
            ));

            DB::commit();

            return $paymentRequest;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Reject the Payment Request
     *
     * @param PaymentRequest $paymentRequest
     *
     * @return PaymentRequest
     *
     * @throws Exception
     */
    public function reject(PaymentRequest $paymentRequest): PaymentRequest
    {
        DB::beginTransaction();
        try {
            $paymentRequest->update([
                'status' => PaymentRequestStatusEnum::REJECTED->value,
            ]);

            WalletService::createWallet($paymentRequest->user, 0, $paymentRequest->getAttribute('amount'), __('Payment Request Rejected'), $paymentRequest);

            $paymentRequest->user->notify(new PaymentRequestRejectedNotification($paymentRequest->getAttribute('amount')));

            $paymentRequest->user->notify(new WalletCreditNotification($paymentRequest->getAttribute('amount')));

            DB::commit();

            return $paymentRequest;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Create payment request
     *
     * @param User $user
     * @param array $data
     *
     * @return PaymentRequest
     *
     * @throws Exception
     */
    public function store(User $user, array $data): PaymentRequest
    {
        DB::beginTransaction();
        try {
            $paymentRequest = PaymentRequest::create([
                'user_id' => $user->getAttribute('id'),
                'status' => PaymentRequestStatusEnum::PENDING->value,
                'amount' => $data['amount'],
                'notes' => $data['notes'],
            ]);

            WalletService::createWallet($user, $data['amount'], 0, __('Payment Request'), $paymentRequest);

            $paymentRequest->user->notify(new PaymentRequestAddedNotification($paymentRequest->getAttribute('amount')));

            $user->notify(new WalletDebitNotification($paymentRequest->getAttribute('amount')));

            DB::commit();

            return $paymentRequest;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }
}
