<?php

namespace App\Http\Requests\Api\Proposal;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * A class define for the edit proposal request
 */
class EditProposalRequest extends FormRequest
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
            'description' => ['sometimes', 'string'],
            'price' => ['sometimes', 'numeric', 'min:1'],
            'time' => ['sometimes', 'integer', 'min:1'],
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
            'description.string' => __('Description must be a string'),
            'price.numeric' => __('price must be a number'),
            'price.min' => __('price must be at least 1'),
            'time.integer' => __('Time must be an integer'),
            'time.min' => __('Time must be at least 1'),
        ];
    }
}
