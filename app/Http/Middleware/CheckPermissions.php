<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\Exceptions\UnauthorizedException;

class CheckPermissions
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string $permission
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permission): mixed
    {
        $user = $request->user();

        if (!$user) {
            throw UnauthorizedException::notLoggedIn();
        }

        if (!in_array($permission, $user->getAllPermissions()->pluck('name')->toArray())) {
            throw UnauthorizedException::forPermissions([$permission]);
        }

        return $next($request);
    }
}
