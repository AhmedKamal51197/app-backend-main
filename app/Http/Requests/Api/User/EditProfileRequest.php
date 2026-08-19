<?php

namespace App\Http\Requests\Api\User;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * A class defined for the edit profile
 */
class EditProfileRequest extends FormRequest
{
    use RequestFailedValidationJsonResponse;

    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Define the rules
     *
     * @return array[]
     */
    public function rules(): array
    {
        $userId = auth()->id();
        
        return [
            'name' => ['nullable', 'min:3'],
            'username' => ['nullable', 'string', 'min:3', 'max:50', 'unique:users,username,' . $userId, 'regex:/^[a-zA-Z0-9_]+$/'],
            'about' => ['nullable', 'min:40', 'max:500'],
            'avatar' => ['nullable', 'file', 'image', 'mimes:jpeg,jpg,png,gif,svg,webp', 'max:8000',]
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
            'name.min' => __('Name must be at least 3 characters'),
            'username.min' => __('Username must be at least 3 characters'),
            'username.max' => __('Username must not exceed 50 characters'),
            'username.unique' => __('Username is already taken'),
            'username.regex' => __('Username can only contain letters, numbers, and underscores'),
            'about.min' => __('About must be at least 40 characters'),
            'about.max' => __('About must not exceed 500 characters'),
            'avatar.file' => __('Avatar must be a valid file'),
            'avatar.image' => __('Avatar must be an image'),
            'avatar.mimes' => __('Avatar must be a file of type: jpeg, jpg, png, gif, svg, webp'),
            'avatar.max' => __('Avatar must not exceed 8000 kilobytes'),
        ];
    }
}
