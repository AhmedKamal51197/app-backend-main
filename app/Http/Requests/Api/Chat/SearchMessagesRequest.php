<?php

namespace App\Http\Requests\Api\Chat;

use App\Enums\MessageTypeEnum;
use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * A class define for searching messages
 */
class SearchMessagesRequest extends FormRequest
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
            'query' => ['required', 'string', 'min:2', 'max:255'],
            'chat_id' => ['nullable', 'exists:chats,id'],
            'type' => ['nullable', Rule::in(MessageTypeEnum::toArray())],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
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
            'query.required' => __('Search query is required'),
            'query.min' => __('Search query must be at least 2 characters'),
            'chat_id.exists' => __('The selected chat does not exist'),
            'type.in' => __('Invalid message type selected'),
            'date_to.after_or_equal' => __('End date must be after or equal to start date'),
        ];
    }
}
