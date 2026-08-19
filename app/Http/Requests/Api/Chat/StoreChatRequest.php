<?php

namespace App\Http\Requests\Api\Chat;

use App\Enums\ChatTypeEnum;
use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * A class define for the store chat
 */
class StoreChatRequest extends FormRequest
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
            'type' => ['required', Rule::in(ChatTypeEnum::toArray())],
            'participant_ids' => ['required', 'array', 'min:1'],
            'participant_ids.*' => ['integer', 'exists:users,id', 'different:' . auth()->id()],
            'chattable_type' => ['nullable', 'string'],
            'chattable_id' => ['nullable', 'integer'],
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
            'type.required' => __('Chat type is required'),
            'type.in' => __('Invalid chat type selected'),
            'participant_ids.required' => __('At least one participant is required'),
            'participant_ids.min' => __('At least one participant is required'),
            'participant_ids.*.exists' => __('One or more selected participants are invalid'),
            'participant_ids.*.different' => __('You cannot add yourself as a participant'),
        ];
    }
}
