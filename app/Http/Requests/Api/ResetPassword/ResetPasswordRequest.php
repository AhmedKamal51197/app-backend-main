<?php

namespace App\Http\Requests\Api\ResetPassword;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ResetPasswordRequest
 *
 * A class defines to send sms request validation
 */
class ResetPasswordRequest extends FormRequest
{
    use RequestFailedValidationJsonResponse;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'exists:users,email'],
            'otp' => ['required', 'string', 'size:6'],
            'password' => ['required'],
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
            'email.exists' => __('The provided email does not exist'),
            'otp.required' => __('OTP is required'),
            'otp.string' => __('OTP must be a string'),
            'otp.size' => __('OTP must be exactly 6 characters'),
            'password.required' => __('Password is required'),
        ];
    }
}
