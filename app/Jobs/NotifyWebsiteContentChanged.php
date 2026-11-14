<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * Tells the website to refresh a piece of content right away after it was
 * changed from the dashboard (settings, faqs, privacy_policy, terms_of_use).
 *
 * Optional: does nothing until WEBSITE_REVALIDATE_URL and
 * LANDING_REVALIDATE_SECRET are set. A failure never affects the save, since
 * the website refreshes its content every hour anyway.
 */
class NotifyWebsiteContentChanged
{
    use Dispatchable;
    use Queueable;

    public function __construct(private readonly string $tag)
    {
    }

    /**
     * Send after the HTTP response so the dashboard never waits for it.
     */
    public static function send(string $tag): void
    {
        if (config('services.website.revalidate_url') && config('services.website.revalidate_secret')) {
            self::dispatchAfterResponse($tag);
        }
    }

    public function handle(): void
    {
        try {
            Http::timeout(5)
                ->withToken(config('services.website.revalidate_secret'))
                ->acceptJson()
                ->post(config('services.website.revalidate_url'), ['tag' => $this->tag]);
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
