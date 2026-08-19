<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * A class defines the authenticated administrator users middleware
 */
class AuthenticateAdministrator
{
    /**
     * Handle an incoming request.
     *
     *
     * @return JsonResponse|mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $user = request()->user();
        
        // Check if user has any supervisor role (allowed_user = false)
        $hasSupervisorRole = $user->roles()->where('allowed_user', false)->exists();
        
        if ($hasSupervisorRole) {
            return $next($request);
        } else {
            throw new HttpException(403, __('This is just for administrators and supervisors'));
        }
    }
}
