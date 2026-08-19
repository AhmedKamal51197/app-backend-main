<?php

namespace App\Http\Requests\Api\BankAccount;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * A class define for updating the bank account
 */
class UpdateBankAccountRequest extends FormRequest
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
            'user_name' => ['sometimes', 'string', 'max:255', 'min:5'],
            'iban' => ['sometimes', 'string', 'min:15', 'max:34', 'regex:/^[A-Z]{2}[0-9]{2}[A-Z0-9]+$/'],
            'swift_code' => ['sometimes', 'string', 'regex:/^[A-Z]{6}[A-Z0-9]{2}([A-Z0-9]{3})?$/'],
            'bank_name' => ['sometimes', 'string', 'max:255', 'min:5'],
            'bank_address' => ['sometimes', 'string', 'max:255', 'min:5'],
            'branch_name' => ['sometimes', 'string', 'max:255', 'min:3'],
            'user_address' => ['sometimes', 'string', 'max:255', 'min:5'],
            'country_id' => ['sometimes', 'integer', 'exists:countries,id'],
        ];
    }

    /**
     * Get custom error messages for validator.
     */
    public function messages(): array
    {
        return [
            'user_name.string' => __('User name must be a string'),
            'user_name.max' => __('User name must not exceed 255 characters'),
            'user_name.min' => __('User name must be at least 5 characters'),
            'iban.string' => __('IBAN must be a string'),
            'iban.min' => __('IBAN must be at least 15 characters'),
            'iban.max' => __('IBAN must not exceed 34 characters'),
            'iban.regex' => __('IBAN format is invalid'),
            'swift_code.string' => __('SWIFT code must be a string'),
            'swift_code.regex' => __('SWIFT code format is invalid'),
            'bank_name.string' => __('Bank name must be a string'),
            'bank_name.max' => __('Bank name must not exceed 255 characters'),
            'bank_name.min' => __('Bank name must be at least 5 characters'),
            'bank_address.string' => __('Bank address must be a string'),
            'bank_address.max' => __('Bank address must not exceed 255 characters'),
            'bank_address.min' => __('Bank address must be at least 5 characters'),
            'branch_name.string' => __('Branch name must be a string'),
            'branch_name.max' => __('Branch name must not exceed 255 characters'),
            'branch_name.min' => __('Branch name must be at least 3 characters'),
            'user_address.string' => __('User address must be a string'),
            'user_address.max' => __('User address must not exceed 255 characters'),
            'user_address.min' => __('User address must be at least 5 characters'),
            'country_id.integer' => __('Country must be a valid integer'),
            'country_id.exists' => __('Country is invalid'),
        ];
    }
}