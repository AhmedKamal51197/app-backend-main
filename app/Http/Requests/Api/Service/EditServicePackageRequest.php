<?php

namespace App\Http\Requests\Api\Service;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * A class define for editing a single service package
 */
class EditServicePackageRequest extends FormRequest
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
            'price' => ['nullable', 'numeric', 'min:1'],
            'days' => ['nullable', 'numeric', 'min:1'],
            'revisions' => ['nullable', 'numeric', 'min:1'],
            'feature_ids' => ['nullable', 'array', 'min:0'],
            'feature_ids.*' => ['string', 'exists:features,uuid'],
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
            'title.string' => 'Package title must be a string',
            'title.max' => 'Package title must not exceed 255 characters',
            'price.required' => 'Package price is required',
            'price.numeric' => 'Package price must be a number',
            'price.min' => 'Package price must be at least 1',
            'days.required' => 'Package days is required',
            'days.numeric' => 'Package days must be a number',
            'days.min' => 'Package days must be at least 1',
            'revisions.required' => 'Package revisions is required',
            'revisions.numeric' => 'Package revisions must be a number',
            'revisions.min' => 'Package revisions must be at least 1',
            'feature_ids.required' => 'Package features are required',
            'feature_ids.array' => 'Package features must be an array',
            'feature_ids.min' => 'At least one feature is required',
            'feature_ids.*.exists' => 'Selected feature does not exist',
        ];
    }
}
