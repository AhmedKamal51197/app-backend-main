<?php

namespace App\Http\Requests\Api\Portfolio;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * A class defines the add portfolio image request validator
 */
class AddPortfolioAttachmentRequest extends FormRequest
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
            'attachment' => [
                'required',
                'file',
                'mimetypes:image/*,video/*,application/pdf',
                'max:100000'
            ],];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'attachment.required' => __('Attachment is required'),
            'attachment.file' => __('Attachment must be a valid file'),
            'attachment.mimetypes' => __('Attachment must be an image or video'),
            'attachment.max' => __('Attachment size must not exceed 51200 kilobytes'),
        ];
    }
}
