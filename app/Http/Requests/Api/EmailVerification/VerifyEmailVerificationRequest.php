<?php

namespace App\Http\Requests\Api\EmailVerification;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class VerifyEmailVerificationRequest
 *
 * A class defines for verify email request validation
 */
class VerifyEmailVerificationRequest extends FormRequest
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
            'otp' => ['required', 'string', 'size:6'],
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
            'otp.required' => __('OTP is required'),
            'otp.string' => __('OTP must be a string'),
            'otp.size' => __('OTP must be exactly 6 characters'),
        ];
    }

}
