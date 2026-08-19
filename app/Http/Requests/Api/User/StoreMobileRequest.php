<?php

namespace App\Http\Requests\Api\User;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * A class defines the store of the user mobile request validator
 */
class StoreMobileRequest extends FormRequest
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
            'mobile' => ['required', 'string', 'digits_between:8,14'],
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
            'mobile.required' => __('Mobile number is required'),
            'mobile.string' => __('Mobile number must be a string'),
            'mobile.digits_between' => __('Mobile number must be between 8 and 14 digits'),
        ];
    }
}
