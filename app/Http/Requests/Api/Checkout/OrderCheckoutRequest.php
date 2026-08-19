<?php

namespace App\Http\Requests\Api\Checkout;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class order checkout Request
 *
 * A class defines to store checkout request validation
 */
class OrderCheckoutRequest extends FormRequest
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
            'order_id' => ['required', 'string', 'exists:orders,uuid'],
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
            'order_id.required' => __('Order ID is required.'),
            'order_id.string' => __('Order ID must be a string.'),
            'order_id.exists' => __('The selected order does not exist.'),
        ];
    }

}
