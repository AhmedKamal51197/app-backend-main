<?php

namespace App\Http\Requests\Api\Portfolio;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * A class define for the edit portfolio
 */
class EditPortfolioRequest extends FormRequest
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
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'min:40', 'max:500'],
            'category_id' => ['nullable', 'exists:categories,uuid'],
            'sub_category_id' => ['nullable', 'exists:sub_categories,uuid'],
            'skill_ids' => ['nullable', 'array'],
            'skill_ids.*' => ['exists:skills,uuid'],
            'hidden' => ['nullable', 'boolean'],
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
            'description.string' => __('Description must be a string'),
            'description.min' => __('Description must be at least 40 characters'),
            'description.max' => __('Description must not exceed 500 characters'),
            'category_id.exists' => __('Selected category is invalid'),
            'sub_category_id.exists' => __('Selected sub category is invalid'),
            'skill_ids.array' => __('Skills must be an array'),
            'skill_ids.*.exists' => __('One or more selected skills are invalid'),
            'hidden.boolean' => __('Hidden must be true or false'),
        ];
    }

}
