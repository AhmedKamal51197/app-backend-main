<?php

namespace App\Http\Requests\Admin\Setting;

use Illuminate\Foundation\Http\FormRequest;

/**
 * A class defines the request to update withdrawal settings
 */
class UpdateWithdrawalSettingsRequest extends FormRequest
{
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
            'daily_payment_requests_amount' => ['required', 'numeric', 'min:0'],
            'monthly_payment_requests_amount' => ['required', 'numeric', 'min:0'],
            'minimum_payment_request_amount' => ['required', 'numeric', 'min:0'],
            // Transfer fee charged to the freelancer on each withdrawal.
            'withdrawal_fee_fixed' => ['sometimes', 'numeric', 'min:0'],
            'withdrawal_fee_percentage' => ['sometimes', 'numeric', 'min:0', 'max:100'],
        ];
    }
}
