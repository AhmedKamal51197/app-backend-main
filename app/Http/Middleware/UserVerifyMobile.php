<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * A class defines the check user has mobile middleware
 */
class UserVerifyMobile
{
    use ApiResponse;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     *
     * @return JsonResponse
     */
    public function handle(Request $request, Closure $next): JsonResponse
    {
        $user = request()->user();

        if (empty($user->mobile) || empty($user->mobile_verified_at)) {
            return $this->jsonError(__('Mobile verification is required'));
        }

        return $next($request);
    }
}
