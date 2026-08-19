<?php

namespace App\Http\Requests\Api\Experience;

use App\Enums\EmploymentTypeEnum;
use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * A class define for the store experience
 */
class StoreExperienceRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'company' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'employment_type' => ['required', Rule::in(EmploymentTypeEnum::toArray())],
            'start_date' => ['required', 'date'],
            'is_current' => ['required', 'boolean'],
            'end_date' => ['nullable', 'date',
                'after_or_equal:start_date',
                function ($attribute, $value, $fail) {
                    if (!$this->is_current && !$value) {
                        $fail(__('The end date is required if the job is not current.'));
                    }
                },
            ],
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
            'title.string' => __('Title must be a string'),
            'title.max' => __('Title must not exceed 255 characters'),
            'company.string' => __('Company must be a string'),
            'company.max' => __('Company must not exceed 255 characters'),
            'description.string' => __('Description must be a string'),
            'employment_type.required' => __('Employment type is required'),
            'employment_type.in' => __('Employment type is invalid'),
            'start_date.date' => __('Start date must be a valid date'),
            'is_current.boolean' => __('Is current must be true or false'),
            'end_date.date' => __('End date must be a valid date'),
            'end_date.after_or_equal' => __('End date must be after or equal to start date'),
            'image.file' => __('Image must be a valid file'),
            'image.image' => __('Image must be a valid image'),
            'image.mimes' => __('Image must be a file of type: jpeg, jpg, png, gif, svg, webp'),
            'image.max' => __('Image size must not exceed 8000 kilobytes'),
        ];
    }
}
