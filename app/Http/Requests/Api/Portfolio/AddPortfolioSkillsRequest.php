<?php

namespace App\Http\Requests\Api\Portfolio;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * A class define for adding portfolio skills
 */
class AddPortfolioSkillsRequest extends FormRequest
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
            'skill_ids' => ['required', 'array', 'min:1'],
            'skill_ids.*' => ['exists:skills,uuid'],
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
            'skill_ids.required' => __('Skills are required'),
            'skill_ids.array' => __('Skills must be an array'),
            'skill_ids.min' => __('At least one skill is required'),
            'skill_ids.*.exists' => __('One or more selected skills are invalid'),
        ];
    }
}
