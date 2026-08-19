<?php

namespace App\Http\Middleware;

use App\Enums\ProposalInvitationStatusEnum;
use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * A class defines the middleware that checks whether the invitation is pending and belongs to the invitee
 */
class IsValidInvitationMiddleware
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
        // Extract the proposal UUID and invitation UUID from the route parameters
        $invitation = $request->route('invitation');

        if ($invitation->status != ProposalInvitationStatusEnum::PENDING->value) {
            return $this->jsonError(__('Invitation is not found or not pending.'));
        }

        // Check if the authenticated user's email matches the invitation receiver's email
        if (auth()->user()->email !== $invitation->email) {
            return $this->jsonError('Unauthorized action: You are not the intended recipient of this invitation.', 401);
        }

        return $next($request);
    }
}
