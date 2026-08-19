<?php

namespace App\Http\Requests\Api\User;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * A class defines the store of the change password request validator
 */
class ChangePasswordRequest extends FormRequest
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
            'old_password' => ['required'],
            'password' => ['required', 'min:8', 'confirmed'],
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
            'old_password.required' => __('Old password is required'),
            'password.required' => __('New password is required'),
            'password.min' => __('New password must be at least 8 characters'),
            'password.confirmed' => __('Password confirmation does not match'),
        ];
    }
}
