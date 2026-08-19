<?php

namespace App\Http\Requests\Api\Service;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * A class define for managing service skills
 */
class ManageSkillsRequest extends FormRequest
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
            'skill_ids.*' => ['string', 'exists:skills,uuid'],
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
            'skill_ids.required' => __('Skill IDs are required'),
            'skill_ids.array' => __('Skill IDs must be an array'),
            'skill_ids.min' => __('At least one skill ID is required'),
            'skill_ids.*.string' => __('Each skill ID must be a string'),
            'skill_ids.*.exists' => __('Each skill ID must exist in the skills table'),
        ];
    }
}
