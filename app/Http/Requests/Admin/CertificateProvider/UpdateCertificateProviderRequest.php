<?php

namespace App\Http\Requests\Admin\CertificateProvider;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * A class defined for update certificate provider from admin
 */
class UpdateCertificateProviderRequest extends FormRequest
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
        $certificateProviderId = $this->route('certificate_provider')
            ? $this->route('certificate_provider')->id
            : $this->route('id');

        return [
            'title_ar' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('certificate_providers', 'title_ar')->ignore($certificateProviderId)
            ],
            'title_en' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('certificate_providers', 'title_en')->ignore($certificateProviderId)
            ],
            'slug' => [
                'sometimes',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('certificate_providers', 'slug')->ignore($certificateProviderId)
            ],
            'description' => [
                'sometimes',
                'nullable',
                'string',
                'max:65535'
            ],
            'website_url' => [
                'sometimes',
                'nullable',
                'string',
                'url',
                'max:255'
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
            'website_url.string' => __('Website URL must be a string'),
            'website_url.url'    => __('Website URL must be a valid URL'),
            'website_url.max'    => __('Website URL must not exceed 255 characters'),
            'is_active.boolean'  => __('Is active must be true or false'),
            'logo.file'   => __('Logo must be a valid file'),
            'logo.image'  => __('Logo must be an image'),
            'logo.mimes'  => __('Logo must be one of the following types: jpeg, jpg, png, gif, svg, webp'),
            'logo.max'    => __('Logo must not exceed 8MB in size'),
        ];
    }
}
