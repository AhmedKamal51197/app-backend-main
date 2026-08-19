<?php

namespace App\Http\Requests\Api\Auth;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * A class defines the user registration request validation
 */
class RegisterRequest extends FormRequest
{
    use RequestFailedValidationJsonResponse;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email:rfc,dns', 'unique:users,email'],
            'name' => ['required', 'min:3'],
            'password' => ['required', 'confirmed'],
            'password_confirmation' => ['required'],
            'country_id' => ['required', 'exists:countries,id'],
            'role' => [
                'required',
                'string',
                Rule::in($this->getAllowedRoles()),
            ],
            'fcm_token' => ['nullable', 'string'],
        ];
    }

    /**
     * Fetch allowed roles dynamically from the roles table.
     *
     * @return array
     */
    private function getAllowedRoles(): array
    {
        $data = DB::table('roles')
            ->where('allowed_user', true)
            ->pluck('name')
            ->toArray();

        return $data;
    }

    /**
     * Get custom error messages for validator.
     */
    public function messages(): array
    {
        return [
            'email.required' => __('Email is required'),
            'email.email' => __('Email must be a valid email address'),
            'email.unique' => __('Email must be unique'),
            'name.required' => __('Name is required'),
            'name.min' => __('Name must be at least 3 characters'),
            'password.required' => __('Password is required'),
            'password.confirmed' => __('Password confirmation does not match'),
            'password_confirmation.required' => __('Password confirmation is required'),
            'country_id.required' => __('Country is required'),
            'country_id.exists' => __('Country is invalid'),
            'role.required' => __('Role is required'),
            'role.string' => __('Role must be a string'),
            'role.in' => __('Role is invalid'),
        ];
    }
}
