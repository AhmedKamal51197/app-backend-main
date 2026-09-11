<?php

namespace App\Http\Controllers\Webhook;

use App\Enums\PaymentGatewaysEnum;
use App\Http\Controllers\Controller;
use App\Models\PaymentGateway\PaymentGatewayCheckout;
use App\Services\Payment\TapService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;

/**
 * Tap sends the customer back here with ?tap_id=chg_...
 * The payment is verified server-side, then the customer is sent to the frontend.
 */
class TapReturnController extends Controller
{
    /**
     * @param Request $request
     * @param TapService $service
     *
     * @return RedirectResponse
     */
    public function __invoke(Request $request, TapService $service): RedirectResponse
    {
        $tapId = (string) $request->query('tap_id', '');

        if (preg_match('/^chg_[A-Za-z0-9_]+$/', $tapId) !== 1) {
            return $this->redirect(null, 'failed');
        }

        // Only charges created by us: stops the endpoint from being used to query Tap.
        $checkout = PaymentGatewayCheckout::query()
            ->where('payment_gateway', PaymentGatewaysEnum::TAP->value)
            ->where('gateway_reference', $tapId)
            ->first();

        if (! $checkout) {
            return $this->redirect(null, 'failed');
        }

        try {
            $checkout = $service->syncCharge($tapId);
        } catch (Throwable $exception) {
            report($exception);

            // Not confirmed yet: the webhook or the tap:sync command will finish it.
            return $this->redirect($checkout, 'pending');
        }

        return $this->redirect($checkout, $checkout->getAttribute('is_processed') ? 'paid' : 'failed');
    }

    /**
     * The status in the query string is display-only; the frontend must not trust it for access.
     *
     * @param PaymentGatewayCheckout|null $checkout
     * @param string $status
     *
     * @return RedirectResponse
     */
    private function redirect(?PaymentGatewayCheckout $checkout, string $status): RedirectResponse
    {
        $base = (string) ($status === 'paid' ? config('tap.success_url') : config('tap.failure_url'));
        $query = http_build_query(array_filter([
            'reference' => $checkout?->getAttribute('uuid'),
            'status' => $status,
        ]));

        return redirect()->away($base.(str_contains($base, '?') ? '&' : '?').$query);
    }
}
