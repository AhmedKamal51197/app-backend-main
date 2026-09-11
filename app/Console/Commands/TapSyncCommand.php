<?php

namespace App\Console\Commands;

use App\Services\Payment\TapService;
use Illuminate\Console\Command;

/**
 * Re-check open Tap charges and refunds (safety net for missed webhooks)
 */
class TapSyncCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'tap:sync';

    /**
     * @var string
     */
    protected $description = 'Re-check open Tap charges and refunds with the Tap API';

    /**
     * @param TapService $service
     *
     * @return int
     */
    public function handle(TapService $service): int
    {
        $counts = $service->syncPending();

        $this->info("Synced {$counts['charges']} charges and {$counts['refunds']} refunds ({$counts['errors']} errors).");

        return $counts['errors'] > 0 ? self::FAILURE : self::SUCCESS;
    }
}
