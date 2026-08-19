<?php

namespace App\Http\Requests\Admin\Certificate;

use App\Enums\CertificateLevelEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * A class defined for update certificate from admin
 */
class UpdateCertificateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title_ar' => [
                'sometimes',
                'string',
                'max:255',
                'unique:certificates,title_ar'
            ],
            'title_en' => [
                'sometimes',
                'string',
                'max:255',
                'unique:certificates,title_en'
            ],
            'description' => [
                'sometimes',
                'nullable',
                'string',
                'max:65535'
            ],
            'level' => [
                'sometimes',
                Rule::enum(CertificateLevelEnum::class)
            ],
        ];
    }

    /**
     * Get custom error messages for validator.
     */
    public function messages(): array
    {
        return [
            'title_ar.required' => __('Arabic title is required'),
            'title_ar.string'   => __('Arabic title must be a string'),
            'title_ar.max'      => __('Arabic title must not exceed 255 characters'),
            'title_ar.unique'   => __('Arabic title must be unique'),
            'title_en.required' => __('English title is required'),
            'title_en.string'   => __('English title must be a string'),
            'title_en.max'      => __('English title must not exceed 255 characters'),
            'title_en.unique'   => __('English title must be unique'),
            'slug.string'       => __('Slug must be a string'),
            'slug.max'          => __('Slug must not exceed 255 characters'),
            'slug.regex'        => __('Slug format is invalid'),
            'slug.unique'       => __('Slug must be unique'),
            'description.string' => __('Description must be a string'),
            'description.max'    => __('Description must not exceed 65535 characters'),
            'level.enum'         => __('Level must be a valid value'),
            'is_active.boolean'  => __('Is active must be true or false'),
        ];
    }
}
