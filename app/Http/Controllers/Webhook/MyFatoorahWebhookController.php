<?php

namespace App\Http\Controllers\Webhook;

use App\Enums\PaymentGatewaysEnum;
use App\Http\Controllers\Api\BaseApiController;
use App\Models\PaymentGateway\PaymentGatewayCheckout;
use App\Models\PaymentGateway\PaymentGatewayWebhookLog;
use App\Services\Payment\MyFatoorahService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * A class defines the MyFatoorah webhook controller
 */
class MyFatoorahWebhookController extends BaseApiController
{
    /**
     * Call the service
     *
     * @param MyFatoorahService $service
     */
    public function __construct(protected MyFatoorahService $service)
    {
    }

    /**
     * Handle received Stripe webhooks.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        try {
            Log::info('MyFatoorah Webhook Received', $request->all());

            $payload = $request->all();

            Log::info('MyFatoorah Webhook Payload');

            PaymentGatewayWebhookLog::create([
                'request_body' => json_decode($request->getContent(), true),
                'request_header' => $request->headers->all(),
                'payment_gateway' => PaymentGatewaysEnum::MYFATOORAH->value,
                'is_processed' => false
            ]);

            if (
                isset($payload['Event'], $payload['Data']['TransactionStatus'], $payload['Data']['InvoiceId'], $payload['Data']['CustomerReference']) &&
                $payload['Event'] === "TransactionsStatusChanged" &&
                $payload['Data']['TransactionStatus'] === "SUCCESS"
            ) {
                if ($this->service->checkIsPaid($payload['Data']['InvoiceId'])) {
                    $checkout = PaymentGatewayCheckout::where('uuid', '=', $payload['Data']['CustomerReference'])->first();

                    if ($checkout) {
                        $this->service->webhook($checkout);
                    }
                }
            } else {
                Log::warning("MyFatoorah Webhook Ignored: Invalid status or missing data.");
            }
        } catch (\Throwable $exception) {
            Log::error('MyFatoorah Webhook Error: ' . $exception->getMessage(), [
                'exception' => $exception,
                'payload' => $request->all()
            ]);
        }

        // Always return 200 OK
        return $this->jsonSuccess([]);
    }
}
