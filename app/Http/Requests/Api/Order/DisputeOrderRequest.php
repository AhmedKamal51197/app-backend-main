<?php

namespace App\Http\Requests\Api\Order;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * A class define for the dispute order request
 */
class DisputeOrderRequest extends FormRequest
{
    use RequestFailedValidationJsonResponse;

    /**
     * Determine if the user is authorized to make this request.
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
            'disputed_reason' => ['required', 'string', 'max:1000'],
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
           'disputed_reason.required' => __('Dispute reason is required'),
           'disputed_reason.max' => __('Dispute reason must not exceed 1000 characters'),
           'disputed_reason.string' => __('Dispute reason must be a string'),
        ];
    }
}
