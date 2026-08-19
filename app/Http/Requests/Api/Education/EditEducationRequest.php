<?php

namespace App\Http\Requests\Api\Education;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * A class define for the edit education
 */
class EditEducationRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'graduation_year' => ['nullable', 'integer', 'min:1900', 'max:' . now()->addYears(10)->year,],
            'description' => ['nullable', 'string'],
            'education_degree_id' => ['required', 'exists:education_degrees,uuid'],
            'major' => ['required', 'string', 'max:255', 'min:3'],
            'image' => ['nullable', 'file', 'image', 'mimes:jpeg,jpg,png,gif,svg,webp', 'max:8000',]
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
            'title.required' => __('Title is required'),
            'title.string' => __('Title must be a string'),
            'title.max' => __('Title must not exceed 255 characters'),
            'graduation_year.integer' => __('Graduation year must be an integer'),
            'graduation_year.min' => __('Graduation year must be at least 1900'),
            'graduation_year.max' => __('Graduation year is invalid'),
            'description.string' => __('Description must be a string'),
            'education_degree_id.required' => __('Education degree is required'),
            'education_degree_id.exists' => __('The selected education degree does not exist'),
            'education_major_id.required' => __('Education major is required'),
            'education_major_id.exists' => __('The selected education major does not exist'),
            'image.file' => __('Image must be a valid file'),
            'image.image' => __('Image must be a valid image'),
            'image.mimes' => __('Image must be a file of type: jpeg, jpg, png, gif, svg, webp'),
            'image.max' => __('Image size must not exceed 8000 kilobytes'),
        ];
    }
}
