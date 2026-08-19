<?php

namespace App\Http\Requests\Api\Checkout;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class service Checkout Request
 *
 * A class defines to store checkout request validation
 */
class ServiceCheckoutRequest extends FormRequest
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
            'package_id' => ['required', 'string', 'exists:service_packages,uuid'],
            'payment_method_id' => ['required', 'numeric']
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
            'package_id.required' => __('Package ID is required'),
            'package_id.string' => __('Package ID must be a string'),
            'package_id.exists' => __('The selected package does not exist'),
        ];
    }
}
