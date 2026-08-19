<?php

namespace App\Http\Requests\Api\PaymentRequest;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StorePaymentRequest
 *
 * A class defines to create payment request validation
 */
class StorePaymentRequest extends FormRequest
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
            'notes' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:10'],
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
            'notes.string' => __('Notes must be a string'),
            'notes.max' => __('Notes must not exceed 255 characters'),
            'amount.required' => __('Amount is required'),
            'amount.numeric' => __('Amount must be a number'),
            'amount.min' => __('Amount must be at least 10'),
        ];
    }

}
