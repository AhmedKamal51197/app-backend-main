<?php

namespace App\Http\Requests\Api\Report;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * A class defines the store of the report request validator
 */
class StoreReportRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:1000', 'min:10'],
            'body' => ['required', 'string', 'max:1000', 'min:10'],
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
            'body.required' => __('Body is required'),
            'body.string' => __('Body must be a string'),
            'body.min' => __('Body must be at least 10 characters'),
            'body.max' => __('Body must not exceed 1000 characters'),
        ];
    }

}
