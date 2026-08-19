<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * A class defined to get the currency apis daily
 */
class UpdateExchangeRates extends Command
{
    protected $signature = 'exchange-rates:update';
    protected $description = 'Fetch and cache exchange rates';

    public function handle()
    {
        try {
            $url = "https://v6.exchangerate-api.com/v6/2fcfe43b6c3c03c47aa93523/latest/USD";

            $response = Http::get($url);

            if ($response->successful()) {
                $rates = $response->json()['conversion_rates'];
                Cache::put('exchange_rates', $rates, now()->addDay());
                $this->info('Exchange rates updated and cached successfully.');
            } else {
                $this->error('Failed to fetch exchange rates.');
            }
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }
}
