<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * A class defines the app local middleware to manage localization for the app and app responses
 */
class AppLocalMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response|RedirectResponse) $next
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $locale = ($request->header('Accept-Language')) ? $request->header('Accept-Language') : app()->getLocale();
        if (!in_array($locale, ['en', 'ar'])) {
            $locale = app()->getLocale();
        }
        app()->setLocale($locale);

        return $next($request);
    }
}
