<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\JsonResponse;

/**
 * A class defines the user logout controller action
 */
class LogoutController extends BaseApiController
{
    public function __invoke(): JsonResponse
    {
        request()->user()->tokens()->delete();

        return $this->jsonSuccess([], 'Logout successfully');
    }
}
