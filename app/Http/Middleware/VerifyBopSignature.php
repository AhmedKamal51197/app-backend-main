<?php

namespace App\Http\Middleware;

use App\Models\Invoice;
use App\Services\Payment\BopSignatureService;
use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class VerifyBopSignature
{
    use ApiResponse;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return JsonResponse
     */
    public function handle(Request $request, Closure $next): JsonResponse
    {
        $invoiceId = $request->input('OrderID');
        $invoice = Invoice::where(['invoice_code' => $invoiceId])->firstOrFail();

        if ($request->input('Signature') !== (new BopSignatureService(orderId: $invoice->getAttribute('invoice_code'), amount: $invoice->getAttribute('due_amount')))->generate()) {

            Log::error('Signature is not correct', [
                'request' => $request->all()
            ]);

            return $this->jsonError(__('Error while verification the payment, please contact support'));
        }

        return $next($request);
    }
}
