<?php

namespace App\Http\Requests\Api\Order;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreOrderMessageRequest
 *
 * A class defines to store order message validation
 */
class StoreOrderMessageRequest extends FormRequest
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
            'text' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'max:8000',]
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
            'text.string' => __('Text must be a string'),
            'file.file' => __('File must be a valid file'),
            'file.max' => __('File size must not exceed 8000 kilobytes'),
        ];
    }
}
