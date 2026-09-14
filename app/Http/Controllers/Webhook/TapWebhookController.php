<?php

namespace App\Http\Controllers\Webhook;

use App\Enums\PaymentGatewaysEnum;
use App\Http\Controllers\Controller;
use App\Models\PaymentGateway\PaymentGatewayWebhookLog;
use App\Services\Payment\Tap\TapSignature;
use App\Services\Payment\Tap\TapUnknownReferenceException;
use App\Services\Payment\TapService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * A class defines the Tap webhook controller
 */
class TapWebhookController extends Controller
{
    /**
     * Handle received Tap webhooks (charges and refunds)
     *
     * @param Request $request
     * @param TapService $service
     *
     * @return JsonResponse
     */
    public function __invoke(Request $request, TapService $service): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);

        if (! is_array($payload) || empty($payload['id']) || empty($payload['status'])) {
            return response()->json(['error' => 'Invalid payload'], 400);
        }

        $log = PaymentGatewayWebhookLog::create([
            'request_body' => $payload,
            'request_header' => [
                'hashstring' => $request->header('hashstring'),
                'user-agent' => $request->userAgent(),
                'ip' => $request->ip(),
            ],
            'payment_gateway' => PaymentGatewaysEnum::TAP->value,
            'is_processed' => false,
        ]);

        if (! TapSignature::verify($payload, $request->header('hashstring'), (string) config('tap.secret_key'))) {
            Log::warning('Rejected Tap webhook with an invalid signature', ['id' => $payload['id'], 'ip' => $request->ip()]);

            return response()->json(['error' => 'Invalid signature'], 401);
        }

        try {
            // The webhook only tells us what changed: the status is re-fetched from Tap.
            match ($this->objectType($payload)) {
                'charge' => $service->syncCharge((string) $payload['id']),
                'refund' => $service->syncRefund((string) $payload['id']),
                default => null,
            };
        } catch (TapUnknownReferenceException $exception) {
            // Not ours: acknowledge so Tap stops retrying.
            Log::info($exception->getMessage());
        } catch (Throwable $exception) {
            report($exception);

            // Tap retries on failure; the tap:sync command is the safety net after that.
            return response()->json(['error' => 'Temporary failure'], 500);
        }

        $log->update(['is_processed' => true]);

        return response()->json(['received' => true]);
    }

    /**
     * @param array $payload
     *
     * @return string
     */
    private function objectType(array $payload): string
    {
        $object = strtolower((string) ($payload['object'] ?? ''));

        if (in_array($object, ['charge', 'refund'], true)) {
            return $object;
        }

        return match (true) {
            str_starts_with((string) $payload['id'], 'chg_') => 'charge',
            str_starts_with((string) $payload['id'], 're_') => 'refund',
            default => 'unknown',
        };
    }
}
