<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * A class defines the exception to unauthorized requests
 */
class AuthorizationException extends Exception
{
    /**
     * @var int|mixed
     */
    protected int $status;

    /**
     * @param $message
     *
     * @param $status
     */
    public function __construct($message = 'This action is unauthorized.', $status = Response::HTTP_FORBIDDEN)
    {
        parent::__construct($message);
        $this->status = $status;
    }

    /**
     * @param $request
     *
     * @return JsonResponse
     */
    public function render($request): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
        ], $this->status);
    }
}
