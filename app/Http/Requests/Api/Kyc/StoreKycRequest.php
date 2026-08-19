<?php

namespace App\Http\Requests\Api\Kyc;

use App\Enums\AttachmentDocumentTypeEnum;
use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Class StoreKycRequest
 *
 * A class defines to store kyc request validation
 */
class StoreKycRequest extends FormRequest
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
            'first_name' => ['required', 'string', 'max:255', 'min:2'],
            'last_name' => ['required', 'string', 'max:255', 'min:2'],
            'birth_date' => ['required', 'date_format:Y-m-d'],
            'country_id' => ['required', 'exists:countries,uuid'],
            'kyc_attachments' => ['required', 'array', 'size:3'],
            'kyc_attachments.*.file' => ['required', 'file', 'mimes:jpeg,png,jpg', 'max:10240'],
            'kyc_attachments.*.file_type' => [
                'required',
                Rule::in([
                    AttachmentDocumentTypeEnum::IDENTITY_CARD_FRONT->value,
                    AttachmentDocumentTypeEnum::IDENTITY_CARD_BACK->value,
                    AttachmentDocumentTypeEnum::USER_HOLDING_ID->value,
                ])
            ],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->has('kyc_attachments')) {
                $fileTypes = collect($this->input('kyc_attachments'))->pluck('file_type');

                $requiredTypes = [
                    AttachmentDocumentTypeEnum::IDENTITY_CARD_FRONT->value,
                    AttachmentDocumentTypeEnum::IDENTITY_CARD_BACK->value,
                    AttachmentDocumentTypeEnum::USER_HOLDING_ID->value,
                ];

                // Check if all required types are present
                foreach ($requiredTypes as $type) {
                    if (!$fileTypes->contains($type)) {
                        $validator->errors()->add('kyc_attachments', __('Missing required attachment type: ' . $type));
                    }
                }

                // Check for duplicates
                if ($fileTypes->count() !== $fileTypes->unique()->count()) {
                    $validator->errors()->add('kyc_attachments', __('Duplicate file types are not allowed'));
                }
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'first_name.required' => __('First name is required'),
            'first_name.string' => __('First name must be a string'),
            'first_name.max' => __('First name must not exceed 255 characters'),
            'first_name.min' => __('First name must be at least 2 characters'),
            'last_name.required' => __('Last name is required'),
            'last_name.string' => __('Last name must be a string'),
            'last_name.max' => __('Last name must not exceed 255 characters'),
            'last_name.min' => __('Last name must be at least 2 characters'),
            'birth_date.required' => __('Birth date is required'),
            'birth_date.date_format' => __('Birth date must be in the format Y-m-d'),
            'country_id.required' => __('Country is required'),
            'country_id.exists' => __('Selected country is invalid'),
            'kyc_attachments.required' => __('KYC attachments are required'),
            'kyc_attachments.array' => __('KYC attachments must be an array'),
            'kyc_attachments.size' => __('Exactly 3 KYC attachments are required: ID front, ID back, and photo holding ID'),
            'kyc_attachments.*.file.required' => __('Each attachment file is required'),
            'kyc_attachments.*.file.file' => __('Each attachment must be a valid file'),
            'kyc_attachments.*.file.mimes' => __('Each attachment file must be a jpeg, png, or jpg image'),
            'kyc_attachments.*.file.max' => __('Each attachment file must not exceed 10MB'),
            'kyc_attachments.*.file_type.required' => __('Each attachment file type is required'),
            'kyc_attachments.*.file_type.in' => __('Invalid file type. Required: identity_card_front, identity_card_back, user_holding_id'),
        ];
    }

}
