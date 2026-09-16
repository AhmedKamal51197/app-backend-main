<?php

namespace App\Services\Payment\Tap;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Thin HTTP client for the Tap v2 API
 */
class TapClient
{
    public function createCharge(array $payload): array
    {
        return $this->send('post', 'charges', $payload);
    }

    public function retrieveCharge(string $chargeId): array
    {
        return $this->send('get', 'charges/'.rawurlencode($chargeId));
    }

    public function createRefund(array $payload): array
    {
        return $this->send('post', 'refunds', $payload);
    }

    public function retrieveRefund(string $refundId): array
    {
        return $this->send('get', 'refunds/'.rawurlencode($refundId));
    }

    /**
     * Only reads are retried: retrying a POST could create a second charge or refund.
     */
    private function send(string $method, string $uri, array $payload = []): array
    {
        $attempts = $method === 'get' ? 3 : 1;

        for ($attempt = 1; ; $attempt++) {
            try {
                $response = $method === 'get'
                    ? $this->request()->get($uri)
                    : $this->request()->post($uri, $payload);

                break;
            } catch (ConnectionException $exception) {
                if ($attempt >= $attempts) {
                    throw new TapException('Could not reach Tap: '.$exception->getMessage(), 0, $exception);
                }

                usleep(500000);
            }
        }

        return $this->decode($uri, $response);
    }

    private function decode(string $uri, Response $response): array
    {
        $body = $response->json();

        if ($response->failed()) {
            $message = data_get($body, 'errors.0.description') ?? data_get($body, 'message') ?? 'HTTP '.$response->status();

            Log::warning('Tap API error', ['uri' => $uri, 'status' => $response->status(), 'body' => $body]);

            throw new TapException("Tap API error: {$message}", $response->status());
        }

        if (! is_array($body)) {
            throw new TapException('Tap returned an invalid response.');
        }

        return $body;
    }

    private function request(): PendingRequest
    {
        $secretKey = (string) config('tap.secret_key');

        if ($secretKey === '') {
            throw new TapException('TAP_SECRET_KEY is not configured.');
        }

        return Http::baseUrl(rtrim((string) config('tap.base_url'), '/'))
            ->withToken($secretKey)
            ->withHeaders(['lang_code' => (string) config('tap.lang_code', 'en')])
            ->acceptJson()
            ->asJson()
            ->timeout((int) config('tap.timeout', 20))
            ->withOptions(['connect_timeout' => 10]);
    }
}
