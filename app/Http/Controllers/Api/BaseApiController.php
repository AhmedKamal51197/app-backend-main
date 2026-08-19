<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;

/**
 * A class defines the base api controller
 */
class BaseApiController extends Controller
{
    use ApiResponse;
}
