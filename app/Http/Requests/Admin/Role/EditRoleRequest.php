<?php

namespace App\Http\Requests\Admin\Role;

use App\Enums\RoleAccessLevelEnum;
use App\Enums\RoleTypeEnum;
use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * A class defines the edit role
 */
class EditRoleRequest extends FormRequest
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
        $roleId = $this->route('role')->id;

        return [
            'name' => [
                'nullable',
                'min:3',
                Rule::unique('roles', 'name')
                    ->where('guard_name', 'api')
                    ->ignore($roleId)
            ],
            'name_ar' => ['nullable', 'string', 'min:3'],
            'allowed_user' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string'],
            'type' => ['nullable', 'string', Rule::in(RoleTypeEnum::toArray())],
            'access_level' => ['nullable', 'string', Rule::in(RoleAccessLevelEnum::toArray())],
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ];
    }

    /**
     * Get custom error messages for validator.
     */
    public function messages(): array
    {
        return [
            'name.required' => __('Role name is required'),
            'name.min' => __('Role name must be at least 3 characters'),
            'name.unique' => __('This role name already exists'),
            'name_ar.string' => __('Arabic name must be a string'),
            'name_ar.min' => __('Arabic name must be at least 3 characters'),
            'allowed_user.required' => __('Allowed user is required'),
            'allowed_user.boolean' => __('Allowed user must be true or false'),
            'is_active.boolean' => __('Is active must be true or false'),
            'description.string' => __('Description must be a string'),
        ];
    }
}
