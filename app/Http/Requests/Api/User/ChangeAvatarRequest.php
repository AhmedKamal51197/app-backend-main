<?php

namespace App\Http\Requests\Api\User;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * A class defined for the change user avatar
 */
class ChangeAvatarRequest extends FormRequest
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
        return [
            'avatar' => ['required', 'file', 'image', 'mimes:jpeg,jpg,png,gif,svg,webp', 'max:8000',]
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
            'avatar.required' => __('Avatar is required'),
            'avatar.file' => __('Avatar must be a valid file'),
            'avatar.image' => __('Avatar must be an image'),
            'avatar.mimes' => __('Avatar must be a file of type: jpeg, jpg, png, gif, svg, webp'),
            'avatar.max' => __('Avatar must not exceed 8000 kilobytes'),
        ];
    }
}
