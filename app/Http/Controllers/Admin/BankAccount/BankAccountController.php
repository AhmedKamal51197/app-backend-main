<?php

namespace App\Http\Controllers\Admin\BankAccount;

use App\Enums\PaymentRequestStatusEnum;
use App\Http\Controllers\Admin\BaseAdminController;
use App\Http\Resources\Admin\BankAccount\BankAccountResource;
use App\Models\Setting;
use App\Services\BankAccount\AdminBankAccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Admin access to users' bank accounts: listing and CSV exports.
 */
class BankAccountController extends BaseAdminController
{
    public function __construct(protected AdminBankAccountService $service)
    {
    }

    /**
     * List all users' bank accounts.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return $this->jsonSuccess(
            BankAccountResource::collection($this->service->index($request->input('limit', Setting::PAGE_RESULT_LIMIT)))
        );
    }

    /**
     * Export all bank accounts as a CSV file.
     *
     * @return StreamedResponse
     */
    public function export(): StreamedResponse
    {
        $accounts = $this->service->allBankAccounts();

        $headers = [
            'الاسم', 'البريد الإلكتروني', 'الجوال', 'اسم صاحب الحساب', 'IBAN', 'SWIFT',
            'البنك', 'الفرع', 'عنوان البنك', 'عنوان المستخدم', 'الدولة', 'تاريخ الإضافة',
        ];

        $rows = $accounts->map(fn ($account) => [
            $account->user?->name,
            $account->user?->email,
            $account->user?->mobile,
            $account->user_name,
            $account->iban,
            $account->swift_code,
            $account->bank_name,
            $account->branch_name,
            $account->bank_address,
            $account->user_address,
            $account->country?->name(),
            $account->created_at?->format('Y-m-d H:i'),
        ]);

        return $this->streamCsv('bank-accounts', $headers, $rows);
    }

    /**
     * Export the withdrawal requests together with the requester's bank
     * details (a payout sheet). Filtered by status, defaults to pending.
     *
     * @param Request $request
     *
     * @return StreamedResponse
     */
    public function exportWithdrawals(Request $request): StreamedResponse
    {
        $status = $request->input('status', PaymentRequestStatusEnum::PENDING->value);

        $requests = $this->service->withdrawalRequests($status);

        $headers = [
            'الاسم', 'البريد الإلكتروني', 'الجوال', 'المبلغ المطلوب', 'الرسوم', 'الصافي المحوّل',
            'الحالة', 'IBAN', 'SWIFT', 'البنك', 'الفرع', 'اسم صاحب الحساب', 'تاريخ الطلب',
        ];

        $rows = $requests->map(function ($paymentRequest) {
            $bank = $paymentRequest->user?->bankAccount;

            return [
                $paymentRequest->user?->name,
                $paymentRequest->user?->email,
                $paymentRequest->user?->mobile,
                $paymentRequest->amount,
                $paymentRequest->fee ?? 0,
                $paymentRequest->net_amount ?? $paymentRequest->amount,
                $paymentRequest->status,
                $bank?->iban,
                $bank?->swift_code,
                $bank?->bank_name,
                $bank?->branch_name,
                $bank?->user_name,
                $paymentRequest->created_at?->format('Y-m-d H:i'),
            ];
        });

        return $this->streamCsv('withdrawal-requests', $headers, $rows);
    }

    /**
     * Stream a UTF-8 CSV download (with BOM so Excel renders Arabic correctly).
     *
     * @param string $name
     * @param array $headers
     * @param iterable $rows
     *
     * @return StreamedResponse
     */
    private function streamCsv(string $name, array $headers, iterable $rows): StreamedResponse
    {
        $filename = $name . '-' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($headers, $rows) {
            $output = fopen('php://output', 'w');

            // UTF-8 BOM so Excel opens Arabic correctly.
            fwrite($output, "\xEF\xBB\xBF");

            fputcsv($output, $headers);

            foreach ($rows as $row) {
                fputcsv($output, $row);
            }

            fclose($output);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
