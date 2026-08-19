<?php

namespace App\Http\Requests\Api\Job;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * A class define for the store job
 */
class StoreJobRequest extends FormRequest
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
            'role' => ['required', 'string', 'max:255'],
            'description_ar' => ['required', 'string'],
            'description_en' => ['required', 'string'],
            'category_id' => ['required', 'string', 'exists:categories,uuid'],
            'weakly_salary' => ['required', 'numeric', 'min:0'],
            'monthly_salary' => ['required', 'numeric', 'min:0'],
            'working_hours' => ['required', 'numeric', 'min:0'],
            'attachments' => ['required', 'array', 'min:1', 'max:5'],
            'attachments.*' => ['file', 'image', 'mimes:jpeg,jpg,png,gif,svg,webp', 'max:8000'],
            'skill_ids' => ['sometimes', 'array', 'min:1'],
            'skill_ids.*' => ['string', 'exists:skills,uuid'],
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
            'role.string' => __('Role must be a string'),
            'role.max' => __('Role must not exceed 255 characters'),
            'description_ar.string' => __('Arabic description must be a string'),
            'description_en.string' => __('English description must be a string'),
            'category_id.exists' => __('The selected category does not exist'),
            'weakly_salary.numeric' => __('Weakly salary must be a number'),
            'weakly_salary.min' => __('Weakly salary must be at least 0'),
            'monthly_salary.numeric' => __('Monthly salary must be a number'),
            'monthly_salary.min' => __('Monthly salary must be at least 0'),
            'working_hours.numeric' => __('Working hours must be a number'),
            'working_hours.min' => __('Working hours must be at least 0'),
        ];
    }
}
