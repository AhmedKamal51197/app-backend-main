<?php

namespace App\Http\Requests\Api\Chat;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * A class define for bulk deleting messages
 */
class BulkDeleteMessagesRequest extends FormRequest
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
            'message_ids' => ['required', 'array', 'min:1', 'max:50'],
            'message_ids.*' => ['integer', 'exists:messages,id'],
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
            'message_ids.required' => __('At least one message must be selected'),
            'message_ids.min' => __('At least one message must be selected'),
            'message_ids.max' => __('You can delete maximum 50 messages at once'),
            'message_ids.*.exists' => __('One or more selected messages do not exist'),
        ];
    }
}
