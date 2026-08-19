<?php

namespace App\Http\Requests\Api\Admin\CommissionSettings;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * A class define for updating commission settings
 */
class UpdateCommissionSettingRequest extends FormRequest
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
            'value' => ['required', 'numeric', 'min:0', 'max:9999'],
            'description' => ['sometimes', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom error messages for validator.
     */
    public function messages(): array
    {
        return [
            'value.required' => __('Commission value is required'),
            'value.numeric' => __('Commission value must be a number'),
            'value.min' => __('Commission value must be at least 0'),
            'value.max' => __('Commission value must not exceed 100'),
            'description.string' => __('Description must be a string'),
            'description.max' => __('Description must not exceed 255 characters'),
        ];
    }
}
