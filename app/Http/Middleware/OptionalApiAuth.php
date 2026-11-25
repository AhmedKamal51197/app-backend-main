<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Public browsing endpoints (guest mode in the app): a visitor without a
 * token gets the guest response, while a signed-in user sending a valid
 * Bearer token is still recognised (favorites, interests).
 * An invalid or expired token is treated as a guest, never an error.
 */
class OptionalApiAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->bearerToken()) {
            $user = Auth::guard('api')->user();

            if ($user) {
                Auth::setUser($user);
                $request->setUserResolver(fn () => $user);
            }
        }

        return $next($request);
    }
}
