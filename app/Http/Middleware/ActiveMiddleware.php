<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * A class defines check if the user is suspended or not
 */
class ActiveMiddleware
{
    use ApiResponse;

    /**
     * Handle an incoming request.
     *
     * @return JsonResponse|mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (Auth::check()) {
            if (!(request()->user()->active)) {
                return $this->jsonError(__('The account is not active, Contact support to enable your account. '));
            }
        }
        return $next($request);
    }
}
