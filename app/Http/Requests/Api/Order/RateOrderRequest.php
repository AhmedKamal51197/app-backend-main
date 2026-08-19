<?php

namespace App\Http\Requests\Api\Order;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class Rate order
 *
 * A class defines to rate order validation
 */
class RateOrderRequest extends FormRequest
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
            'rate' => ['required', 'numeric', 'min:3', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
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
            'rate.required' => __('Rate is required'),
            'rate.numeric' => __('Rate must be a number'),
            'rate.min' => __('Rate must be at least 3'),
            'rate.max' => __('Rate must not exceed 5'),
            'comment.string' => __('Comment must be a string'),
            'comment.max' => __('Comment must not exceed 1000 characters'),
        ];
    }

}
