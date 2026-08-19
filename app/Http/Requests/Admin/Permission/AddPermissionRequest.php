<?php

namespace App\Http\Requests\Admin\Permission;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * A class defines the add permission
 */
class AddPermissionRequest extends FormRequest
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
            'name' => [
                'required', 
                'min:3',
                Rule::unique('permissions', 'name')->where('guard_name', 'api')
            ],
            'name_ar' => ['nullable', 'string', 'min:3'],
        ];
    }

    /**
     * Get custom error messages for validator.
     */
    public function messages(): array
    {
        return [
            'name.required' => __('Permission name is required'),
            'name.min'      => __('Permission name must be at least 3 characters'),
            'name.unique'   => __('This permission name already exists'),
            'name_ar.string' => __('Arabic name must be a string'),
            'name_ar.min' => __('Arabic name must be at least 3 characters'),
        ];
    }
}
