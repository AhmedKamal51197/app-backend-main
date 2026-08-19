<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\Response;

/**
 * A trait class defines the general api json responses
 */
trait ApiResponse
{
    /**
     * Generate successful json response
     */
    public function jsonSuccess(
        mixed $data,
        string $message = '',
        int $code = Response::HTTP_OK
    ): JsonResponse {
        $response = [
            'error' => false,
            'message' => $message,
        ];

        // If it's a paginated result (either paginator or resource collection with pagination)
        if ($data instanceof LengthAwarePaginator || $data instanceof ResourceCollection && $data->resource instanceof LengthAwarePaginator) {
            $response['data'] = $data;
            $paginator = $data instanceof ResourceCollection ? $data->resource : $data;

            $response['meta'] = [
                'currentPage' => $paginator->currentPage(),
                'lastPage' => $paginator->lastPage(),
                'perPage' => $paginator->perPage(),
                'total' => $paginator->total(),
            ];
        } else {
            $response['data'] = $data;
        }

        return response()->json($response, $code, [], JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Generate successful json response without convert string to numeric values
     */
    public function jsonSuccessWithStrings(mixed $data, string $message = '', int $code = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'error' => false,
            'message' => $message,
            'data' => $data,
        ], $code, []);
    }

    /**
     * * Generate failure json response
     */
    public function jsonError(?string $message, int $code = Response::HTTP_BAD_REQUEST, string $debug = ''): JsonResponse
    {
        $response = [
            'error' => true,
            'message' => $message,
            'data' => [],
        ];

        if (!app()->environment('production')) {
            $response['debug'] = $debug;
        }

        return response()->json($response, $code);
    }
}
