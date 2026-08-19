<?php

namespace App\Http\Requests\Api\Response;

use App\Enums\AttachmentDocumentTypeEnum;
use App\Enums\MessageTypeEnum;
use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * A class defines the store response request
 */
class StoreResponseRequest extends FormRequest
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
            'content' => ['required_without:attachments', 'nullable', 'string', 'max:5000'],
            'type' => ['nullable', Rule::in(MessageTypeEnum::toArray())],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*.file' => ['required', 'file', 'max:10240'], // 10MB max
            'attachments.*.file_type' => ['required', Rule::in(AttachmentDocumentTypeEnum::toArray())],
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
            'content.required_without' => __('Response content is required when no attachments are provided'),
            'content.max' => __('Response content cannot exceed 5000 characters'),
            'attachments.max' => __('You can upload a maximum of 5 attachments'),
            'attachments.*.file.required' => __('Attachment file is required'),
            'attachments.*.file.max' => __('Attachment file size cannot exceed 10MB'),
            'attachments.*.file_type.required' => __('Attachment file type is required'),
        ];
    }
}
