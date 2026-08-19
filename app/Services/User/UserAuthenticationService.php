<?php

namespace App\Services\User;

use App\Http\Resources\Api\User\UserResource;
use App\Models\User;
use Carbon\Carbon;

/**
 * A class defines the user authentication service
 */
class UserAuthenticationService
{
    /**
     * Get authentication token
     *
     * @param User $user
     *
     * @return array
     */
    public static function getToken(User $user): array
    {
        $tokenResult = $user->createToken('Personal Access Token UID: ' . $user->getAttribute('id'));
        $token = $tokenResult->token;
        $token->save();

        $user->load(['category', 'country', 'roles']);

        $isAdminOrSupervisor = $user->roles()->where('allowed_user', false)->exists();
        $permissions = [];
        
        if ($isAdminOrSupervisor) {
            $permissions = $user->getAllPermissions()->pluck('name')->toArray();
        }

        return [
            'access_token' => $tokenResult->accessToken,
            'email_verified' => !is_null($user->getAttribute('email_verified_at')),
            'mobile_verified' => !is_null($user->getAttribute('mobile_verified_at')),
            'kyc_verified' => $user->isKycVerified(),
            'profile_verified' => true,
            'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
            'user_data' => UserResource::make($user),
            'permissions' => $permissions,
        ];
    }
}
