<?php

namespace App\Http\Requests\Api\Portfolio;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * A class define for the store portfolio
 */
class StorePortfolioRequest extends FormRequest
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
            'description' => ['required', 'string', 'min:40', 'max:500'],
            'category_id' => ['required', 'exists:categories,uuid'],
            'sub_category_id' => ['required', 'exists:sub_categories,uuid'],
            'skill_ids' => ['required', 'array', 'min:1'],
            'skill_ids.*' => ['exists:skills,uuid'],
            'hidden' => ['nullable','boolean'],
            'attachments' => ['required', 'array'],
            'attachments.*' => [
                'file',
                'mimetypes:image/*,video/*,application/pdf',
                'max:200000',
            ],
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
            'description.required' => __('Description is required'),
            'description.string' => __('Description must be a string'),
            'description.min' => __('Description must be at least 40 characters'),
            'description.max' => __('Description must not exceed 500 characters'),
            'category_id.required' => __('Category is required'),
            'category_id.exists' => __('Selected category is invalid'),
            'sub_category_id.required' => __('Sub category is required'),
            'sub_category_id.exists' => __('Selected sub category is invalid'),
            'skill_ids.array' => __('Skills must be an array'),
            'skill_ids.*.exists' => __('One or more selected skills are invalid'),
            'hidden.boolean' => __('Hidden must be true or false'),
            'attachments.required' => __('Attachments are required'),
            'attachments.array' => __('Attachments must be an array'),
            'attachments.*.file' => __('Each attachment must be a valid file'),
            'attachments.*.mimetypes' => __('Each attachment must be an image or video'),
            'attachments.*.max' => __('Each attachment size must not exceed 51200 kilobytes'),
        ];
    }

}
