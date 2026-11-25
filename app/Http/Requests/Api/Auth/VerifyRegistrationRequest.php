<?php

namespace App\Http\Requests\Api\Auth;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Second sign-up step: the email used in step one and its OTP.
 * `otp` is optional so the same request validates a resend.
 */
class VerifyRegistrationRequest extends FormRequest
{
    use RequestFailedValidationJsonResponse;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'otp' => [$this->routeIs('register.verify') ? 'required' : 'nullable', 'string', 'size:6'],
            'fcm_token' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => __('Email is required'),
            'email.email' => __('Email must be a valid email address'),
            'otp.required' => __('OTP is required'),
            'otp.size' => __('OTP must be exactly 6 characters'),
        ];
    }
}
