<?php

namespace App\Traits;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

/**
 * A trait class defines the request failed validation json response
 */
trait RequestFailedValidationJsonResponse
{
    /**
     * Return fail validation response as json response
     */
    public function failedValidation(Validator $validator): mixed
    {
        throw new HttpResponseException(
            response()->json([
                'error' => true,
                'message' => $validator->errors()->first(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
