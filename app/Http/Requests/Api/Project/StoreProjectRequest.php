<?php

namespace App\Http\Requests\Api\Project;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * A class define for the store project request
 */
class StoreProjectRequest extends FormRequest
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
            'description' => ['required', 'string'],
            'min_price' => ['required', 'numeric', 'min:1'],
            'max_price' => ['required', 'numeric', 'min:1', 'gte:min_price'],
            'time' => ['required', 'integer', 'min:1'],
            'category_id' => ['required', 'string', 'exists:categories,uuid'],
            'sub_category_id' => ['sometimes', 'string', 'exists:sub_categories,uuid'],
            'attachments' => ['sometimes', 'array', 'max:10'],
            'attachments.*' => ['file', 'max:81920'],
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
            'min_price.required' => __('Minimum price is required'),
            'min_price.numeric' => __('Minimum price must be a number'),
            'min_price.min' => __('Minimum price must be at least 1'),
            'max_price.required' => __('Maximum price is required'),
            'max_price.numeric' => __('Maximum price must be a number'),
            'max_price.min' => __('Maximum price must be at least 1'),
            'max_price.gte' => __('Maximum price must be greater than or equal to minimum price'),
            'time.required' => __('Time is required'),
            'time.integer' => __('Time must be an integer'),
            'time.min' => __('Time must be at least 1'),
            'category_id.required' => __('Category is required'),
            'category_id.string' => __('Category ID must be a string'),
            'category_id.exists' => __('Selected category does not exist'),
            'sub_category_id.string' => __('Sub category ID must be a string'),
            'sub_category_id.exists' => __('Selected sub category does not exist'),
            'attachments.array' => __('Attachments must be an array'),
            'attachments.max' => __('You can upload up to 10 attachments'),
            'attachments.*.file' => __('Each attachment must be a file'),
            'attachments.*.max' => __('Each attachment must not exceed 8000 kilobytes'),
        ];
    }
}
