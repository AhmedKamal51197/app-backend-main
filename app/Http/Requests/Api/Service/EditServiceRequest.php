<?php

namespace App\Http\Requests\Api\Service;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * A class define for the edit service.
 */
class EditServiceRequest extends FormRequest
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
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'client_guidelines' => ['nullable', 'string'],
            'category_id' => ['sometimes', 'string', 'exists:categories,uuid'],
            'sub_category_id' => ['sometimes', 'string', 'exists:sub_categories,uuid'],
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
            'client_guidelines.string' => __('Client guidelines must be a string'),
        ];
    }
}
