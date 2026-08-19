<?php

namespace App\Http\Requests\Api\Project;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * A class define for the store project request
 */
class CancelProjectRequest extends FormRequest
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
            'cancellation_reason' => ['required', 'string', 'max:1000'],
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
           'cancellation_reason.max' => __('Cancellation reason must not exceed 1000 characters'),
           'cancellation_reason.required' => __('Cancellation reason is required'),
           'cancellation_reason.string' => __('Cancellation reason must be a string'),
        ];
    }
}
