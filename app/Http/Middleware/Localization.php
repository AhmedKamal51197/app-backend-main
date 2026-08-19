<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * A class defines the localization middleware
 */
class Localization
{
    /**
     * Handle an incoming request.
     *
     *
     * @return RedirectResponse|Response|mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        // Check header request and determine localization
        $local = ($request->hasHeader('app-language')) ? $request->header('app-language') : 'ar';

        // set laravel localization
        app()->setLocale($local);

        // continue request
        return $next($request);
    }
}
