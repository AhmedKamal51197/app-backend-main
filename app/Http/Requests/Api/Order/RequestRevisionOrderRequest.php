<?php

namespace App\Http\Requests\Api\Order;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class RequestRevisionOrderRequest
 *
 * A class defines to store request revision order validation
 */
class RequestRevisionOrderRequest extends FormRequest
{
    use RequestFailedValidationJsonResponse;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {

        return [
            'details' => ['required', 'string', 'min:10', 'max:255'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'details.required' => __('Details are required'),
            'details.string' => __('Details must be a string'),
            'details.min' => __('Details must be at least 10 characters'),
            'details.max' => __('Details must not exceed 255 characters'),
        ];
    }
}
