<?php

namespace App\Http\Requests\Admin\Role;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * A class defines the edit supervisor request validation
 */
class EditSupervisorRequest extends FormRequest
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
        $userId = $this->route('user')->id;
        
        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'mobile' => [
                'required', 
                'string', 
                Rule::unique('users', 'mobile')->ignore($userId)
            ],
            'email' => [
                'required', 
                'email', 
                Rule::unique('users', 'email')->ignore($userId)
            ],
            'role_id' => ['required', 'exists:roles,uuid'],
        ];
    }

    /**
     * Get custom error messages for validator.
     */
    public function messages(): array
    {
        return [
            'name.required' => __('Supervisor name is required'),
            'name.min' => __('Supervisor name must be at least 3 characters'),
            'name.max' => __('Supervisor name must not exceed 255 characters'),
            'mobile.required' => __('Mobile number is required'),
            'mobile.unique' => __('This mobile number is already registered'),
            'email.required' => __('Email is required'),
            'email.email' => __('Please provide a valid email address'),
            'email.unique' => __('This email is already registered'),
            'role_id.required' => __('Role is required'),
            'role_id.exists' => __('Selected role does not exist'),
        ];
    }
}