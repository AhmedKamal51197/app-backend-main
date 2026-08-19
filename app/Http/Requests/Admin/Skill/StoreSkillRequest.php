<?php

namespace App\Http\Requests\Admin\Skill;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreSkillRequest
 *
 * A class defines to store skill request validation
 */
class StoreSkillRequest extends FormRequest
{
    use RequestFailedValidationJsonResponse;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title_ar' => ['required', 'string', 'max:255', 'min:3', 'unique:skills,title_ar'],
            'title_en' => ['required', 'string', 'max:255', 'min:3', 'unique:skills,title_en'],
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
            'title_ar.min'      => __('Arabic title must be at least 3 characters'),
            'title_ar.unique'   => __('Arabic title must be unique'),
            'title_en.required' => __('English title is required'),
            'title_en.string'   => __('English title must be a string'),
            'title_en.max'      => __('English title must not exceed 255 characters'),
            'title_en.min'      => __('English title must be at least 3 characters'),
            'title_en.unique'   => __('English title must be unique'),
        ];
    }
}
