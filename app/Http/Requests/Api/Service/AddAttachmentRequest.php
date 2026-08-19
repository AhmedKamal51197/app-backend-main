<?php

namespace App\Http\Requests\Api\Service;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * A class define for adding attachment to service
 */
class AddAttachmentRequest extends FormRequest
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
            'attachment' => [
                'required',
                'file',
                'mimetypes:image/*,video/*,application/pdf',
                'max:81920'
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
            'attachment.required' => __('An attachment file is required'),
            'attachment.file' => __('The attachment must be a file'),
            'attachment.image' => __('The attachment must be an image'),
            'attachment.mimes' => __('The attachment must be a file of type: jpeg, jpg, png, gif, svg, webp'),
            'attachment.max' => __('The attachment may not be greater than 8MB'),
        ];
    }
}
