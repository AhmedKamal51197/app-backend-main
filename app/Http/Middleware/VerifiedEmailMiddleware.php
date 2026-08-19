<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;

/**
 * A class defines the check user verify email middleware
 */
class VerifiedEmailMiddleware
{
    use ApiResponse;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (!$request->user() || !$request->user()->hasVerifiedEmail()) {
            return $this->jsonError(__('Email verification is required'));
        }

        return $next($request);
    }
}
