<?php

namespace App\Http\Requests\Api\PayPal;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * A class define store PayPal
 */
class StorePayPalRequest extends FormRequest
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
            'email' => ['required', 'email'],
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'user_name' => ['required', 'string', 'min:3', 'max:255'],
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
            'email.required' => __('Email is required'),
            'email.email' => __('Email must be a valid email address'),
            'name.required' => __('Name is required'),
            'name.string' => __('Name must be a string'),
            'name.min' => __('Name must be at least 3 characters'),
            'name.max' => __('Name must not exceed 255 characters'),
            'user_name.required' => __('User name is required'),
            'user_name.string' => __('User name must be a string'),
            'user_name.min' => __('User name must be at least 3 characters'),
            'user_name.max' => __('User name must not exceed 255 characters'),
        ];
    }
}
