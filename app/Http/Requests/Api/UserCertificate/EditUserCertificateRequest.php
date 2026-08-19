<?php

namespace App\Http\Requests\Api\UserCertificate;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * A class defined for the edit user certificate request
 */
class EditUserCertificateRequest extends FormRequest
{
    use RequestFailedValidationJsonResponse;
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Adding the validation rules
     *
     * @return array[]
     */
    public function rules(): array
    {
        return [
            'completion_date' => ['nullable', 'date', 'before_or_equal:today'],
            'expiry_date' => ['nullable', 'date', 'after:completion_date'],
            'credential_id' => ['nullable', 'string', 'max:255'],
            'certificate' => ['nullable', 'file', 'image', 'mimes:jpeg,jpg,png,gif,svg,webp', 'max:8000'],
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
            'completion_date.date' => __('Completion date must be a valid date'),
            'completion_date.before_or_equal' => __('Completion date must be today or earlier'),
            'expiry_date.date' => __('Expiry date must be a valid date'),
            'expiry_date.after' => __('Expiry date must be after the completion date'),
            'credential_id.string' => __('Credential ID must be a string'),
            'credential_id.max' => __('Credential ID may not be greater than 255 characters'),
            'certificate.file' => __('The certificate must be a file'),
            'certificate.image' => __('The certificate must be an image'),
            'certificate.mimes' => __('The certificate must be a file of type: jpeg, jpg, png, gif, svg, webp'),
            'certificate.max' => __('The certificate may not be greater than 8MB'),
        ];
    }
}
