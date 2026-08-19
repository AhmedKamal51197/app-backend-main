<?php

namespace App\Http\Requests\Api\Order;

use App\Enums\AttachmentDocumentTypeEnum;
use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class RequestReleaseOrderRequest
 *
 * A class defines to store request release order validation
 */
class RequestReleaseOrderRequest extends FormRequest
{
    use RequestFailedValidationJsonResponse;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
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
            'details' => ['required', 'string', 'min:3', 'max:255'],
            'attachments' => ['required', 'array', 'min:1', 'max:999'],
            'attachments.*.file' => ['required', 'file', 'max:902400'],
            'attachments.*.file_type' => [
                'required',
                Rule::in([
                    AttachmentDocumentTypeEnum::DOCUMENT->value,
                ]),
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
            'details.required' => __('Details are required'),
            'details.string' => __('Details must be a string'),
            'details.min' => __('Details must be at least 3 characters'),
            'details.max' => __('Details must not exceed 255 characters'),
            'attachments.required' => __('Attachments are required'),
            'attachments.array' => __('Attachments must be an array'),
            'attachments.min' => __('At least 1 attachment is required'),
            'attachments.max' => __('No more than 3 attachments are allowed'),
            'attachments.*.file.required' => __('Each attachment file is required'),
            'attachments.*.file.file' => __('Each attachment must be a valid file'),
            'attachments.*.file.max' => __('Each attachment file must not exceed 102400 kilobytes'),
            'attachments.*.file_type.required' => __('Each attachment file type is required'),
            'attachments.*.file_type.in' => __('Each attachment file type is invalid'),
        ];
    }

}
