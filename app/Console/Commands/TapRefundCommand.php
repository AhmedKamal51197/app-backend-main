<?php

namespace App\Console\Commands;

use App\Models\PaymentGateway\PaymentGatewayCheckout;
use App\Services\Payment\Tap\TapException;
use App\Services\Payment\TapService;
use Illuminate\Console\Command;

/**
 * Refund a paid Tap checkout to the customer's card (full or partial)
 */
class TapRefundCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'tap:refund
        {checkout : Checkout UUID}
        {amount? : Amount to refund, e.g. 25.50 (default: remaining balance)}
        {--reason=requested_by_customer : Reason sent to Tap}';

    /**
     * @var string
     */
    protected $description = 'Refund a paid Tap checkout (full or partial)';

    /**
     * @param TapService $service
     *
     * @return int
     */
    public function handle(TapService $service): int
    {
        $checkout = PaymentGatewayCheckout::where('uuid', $this->argument('checkout'))->first();

        if (! $checkout) {
            $this->error('Checkout not found.');

            return self::FAILURE;
        }

        $amount = $this->argument('amount');

        if ($amount !== null && ! is_numeric($amount)) {
            $this->error('Amount must be a number.');

            return self::FAILURE;
        }

        try {
            $refund = $service->refund($checkout, $amount === null ? null : (float) $amount, (string) $this->option('reason'));
        } catch (TapException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info("Refund {$refund->getAttribute('uuid')}: {$refund->getAttribute('amount')} - {$refund->getAttribute('status')}");

        return self::SUCCESS;
    }
}
