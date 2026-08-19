<?php

namespace App\Http\Requests\Api\UserSkill;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * A class defines the store user skills request
 */
class StoreUserSkillsRequest extends FormRequest
{
    use RequestFailedValidationJsonResponse;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'skill_ids' => 'required|array|min:1',
            'skill_ids.*' => 'required|string|exists:skills,uuid',
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
            'skill_ids.required' => __('At least one skill must be selected'),
            'skill_ids.array' => __('Skills must be provided as an array'),
            'skill_ids.min' => __('At least one skill must be selected'),
            'skill_ids.*.required' => __('Each skill ID is required'),
            'skill_ids.*.string' => __('Each skill ID must be a string'),
            'skill_ids.*.exists' => __('One or more selected skills do not exist'),
        ];
    }
}
