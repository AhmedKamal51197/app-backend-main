<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Headers and language for the public content endpoints (settings, FAQs,
 * pages) read by the website.
 *
 * - Language from Accept-Language, honouring q-values ("en-US,en;q=0.9,ar;q=0.8"),
 *   Arabic when the header is missing or has no supported language.
 * - Content-Language: the language actually returned (a controller may set it
 *   itself, e.g. Arabic fallback for untranslated content).
 * - Vary: Accept-Language so caches never serve one language for the other.
 * - Short public cache (5 minutes).
 */
class PublicContent
{
    public const SUPPORTED = ['ar', 'en'];
    public const DEFAULT = 'ar';

    public function handle(Request $request, Closure $next): Response
    {
        app()->setLocale(self::preferredLanguage($request->header('Accept-Language')));

        /** @var Response $response */
        $response = $next($request);

        if (!$response->headers->has('Content-Language')) {
            $response->headers->set('Content-Language', app()->getLocale());
        }
        $response->headers->set('Vary', 'Accept-Language', false);

        if ($response->isSuccessful()) {
            $response->headers->set('Cache-Control', 'public, max-age=300');
        }

        return $response;
    }

    /**
     * First supported language in the header, by q-value then position.
     */
    public static function preferredLanguage(?string $header): string
    {
        if (!$header) {
            return self::DEFAULT;
        }

        $candidates = [];
        foreach (explode(',', $header) as $position => $part) {
            $pieces = array_map('trim', explode(';', $part));
            $tag = strtolower($pieces[0] ?? '');
            $quality = 1.0;

            foreach (array_slice($pieces, 1) as $param) {
                if (str_starts_with($param, 'q=')) {
                    $quality = (float) substr($param, 2);
                }
            }

            $language = explode('-', $tag)[0];
            if ($quality > 0 && in_array($language, self::SUPPORTED, true)) {
                $candidates[] = ['language' => $language, 'q' => $quality, 'position' => $position];
            }
        }

        if (!$candidates) {
            return self::DEFAULT;
        }

        usort($candidates, fn ($a, $b) => [$b['q'], $a['position']] <=> [$a['q'], $b['position']]);

        return $candidates[0]['language'];
    }
}
