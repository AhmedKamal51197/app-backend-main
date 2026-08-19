<?php

namespace App\Http\Requests\Api\Checkout;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class project Checkout Request
 *
 * A class defines to store checkout request validation
 */
class ProjectCheckoutRequest extends FormRequest
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
            'project_id' => ['required', 'string', 'exists:projects,uuid'],
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
            'project_id.required' => __('Project ID is required'),
            'project_id.string' => __('Project ID must be a string'),
            'project_id.exists' => __('The selected Project does not exist'),
        ];
    }
}
