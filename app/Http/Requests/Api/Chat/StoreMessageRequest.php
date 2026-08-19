<?php

namespace App\Http\Requests\Api\Chat;

use App\Enums\AttachmentDocumentTypeEnum;
use App\Enums\MessageTypeEnum;
use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * A class define for the store message
 */
class StoreMessageRequest extends FormRequest
{
    use RequestFailedValidationJsonResponse;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $chat = $this->route('chat');

        return auth()->check() && $chat->participants()->where('user_id', auth()->id())->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'content' => ['required_without:attachment', 'string'],
            'type' => ['sometimes', Rule::in(MessageTypeEnum::toArray())],
            'reply_to_id' => ['nullable', 'exists:messages,id'],
            'attachments' => ['nullable', 'array', 'max:2'],
            'attachments.*.file' => ['required', 'file', 'max:10240'],
            'attachments.*.file_type' => [
                'required',
                Rule::in([
                   AttachmentDocumentTypeEnum::PICTURE->value,
                   AttachmentDocumentTypeEnum::DOCUMENT->value,
                   AttachmentDocumentTypeEnum::VIDEO->value,
                ])
            ],
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
            'content.required_without' => __('Message content is required when no attachments are provided'),
            'reply_to_id.exists' => __('The message you are replying to does not exist'),
            'attachments.max' => __('You can upload maximum 5 attachments'),
            'attachments.*.file' => __('Each attachment must be a file'),
            'attachments.*.max' => __('Each attachment may not be greater than 10MB'),
        ];
    }

    /**
     * Additional validation after basic rules
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $chat = $this->route('chat');

            // Validate reply_to_id belongs to the same chat
            if ($this->reply_to_id) {
                $replyMessage = \App\Models\Message::find($this->reply_to_id);
                if ($replyMessage && $replyMessage->chat_id !== $chat->id) {
                    $validator->errors()->add('reply_to_id', __('You can only reply to messages in the same chat.'));
                }
            }
        });
    }
}
