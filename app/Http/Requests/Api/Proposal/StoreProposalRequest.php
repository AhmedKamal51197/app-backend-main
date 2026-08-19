<?php

namespace App\Http\Requests\Api\Proposal;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * A class define for the store proposal request
 */
class StoreProposalRequest extends FormRequest
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
            'project_id' => ['required', 'exists:projects,uuid'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:1'],
            'time' => ['required', 'integer', 'min:1'],
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
            'project_id.required' => __('Project ID is required'),
            'description.required' => __('Description is required'),
            'description.string' => __('Description must be a string'),
            'price.required' => __('price is required'),
            'price.numeric' => __('price must be a number'),
            'price.min' => __('price must be at least 1'),
            'time.required' => __('Time is required'),
            'time.integer' => __('Time must be an integer'),
            'time.min' => __('Time must be at least 1'),
            'attachments.array' => __('Attachments must be an array'),
            'attachments.max' => __('You can upload up to 10 attachments'),
            'attachments.*.file' => __('Each attachment must be a file'),
            'attachments.*.max' => __('Each attachment must not exceed 8000 kilobytes'),
        ];
    }
}
