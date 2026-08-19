<?php

namespace App\Http\Requests\Admin\Role;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * A class defines the update supervisor password request validation
 */
class UpdateSupervisorPasswordRequest extends FormRequest
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
     * @return array
     */
    public function rules(): array
    {
        return [
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    /**
     * Get custom error messages for validator.
     */
    public function messages(): array
    {
        return [
            'password.required' => __('Password is required'),
            'password.min' => __('Password must be at least 8 characters'),
            'password.confirmed' => __('Password confirmation does not match'),
        ];
    }
}
