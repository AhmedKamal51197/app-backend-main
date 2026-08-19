<?php

namespace App\Http\Requests\Api\Chat;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * A class define for forwarding messages
 */
class ForwardMessageRequest extends FormRequest
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
            'target_chat_uuid' => ['required', 'string', 'exists:chats,uuid'],
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
            'target_chat_uuid.required' => __('Target chat is required'),
            'target_chat_uuid.exists' => __('The selected target chat does not exist'),
        ];
    }
}
