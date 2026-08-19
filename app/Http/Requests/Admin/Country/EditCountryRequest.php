<?php

namespace App\Http\Requests\Admin\Country;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class EditCountryRequest
 *
 * A class defines to edit country request validation
 */
class EditCountryRequest extends FormRequest
{
    use RequestFailedValidationJsonResponse;

    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $countryId = $this->route('country');

        return [
            'iso' => ['nullable', 'string', 'max:3', 'unique:countries,iso,' . $countryId],
            'iso3' => ['nullable', 'string', 'max:3', 'unique:countries,iso3,' . $countryId],
            'english_name' => ['required', 'string', 'max:255', 'unique:countries,english_name,' . $countryId],
            'arabic_name' => ['required', 'string', 'max:255', 'unique:countries,arabic_name,' . $countryId],
            'phone_code' => ['required', 'string', 'max:10'],
            'currency' => ['nullable', 'string', 'max:50'],
            'currency_code' => ['nullable', 'string', 'max:10'],
            'capital' => ['nullable', 'string', 'max:255'],
            'is_enabled' => ['boolean'],
        ];
    }

    /**
     * Get custom error messages for validator.
     */
    public function messages(): array
    {
        return [
            'iso.string' => __('ISO must be a string'),
            'iso.max' => __('ISO must not exceed 3 characters'),
            'iso.unique' => __('ISO must be unique'),
            'iso3.string' => __('ISO3 must be a string'),
            'iso3.max' => __('ISO3 must not exceed 3 characters'),
            'iso3.unique' => __('ISO3 must be unique'),
            'english_name.required' => __('English name is required'),
            'english_name.string' => __('English name must be a string'),
            'english_name.max' => __('English name must not exceed 255 characters'),
            'english_name.unique' => __('English name must be unique'),
            'arabic_name.required' => __('Arabic name is required'),
            'arabic_name.string' => __('Arabic name must be a string'),
            'arabic_name.max' => __('Arabic name must not exceed 255 characters'),
            'arabic_name.unique' => __('Arabic name must be unique'),
            'phone_code.required' => __('Phone code is required'),
            'phone_code.string' => __('Phone code must be a string'),
            'phone_code.max' => __('Phone code must not exceed 10 characters'),
            'currency.string' => __('Currency must be a string'),
            'currency.max' => __('Currency must not exceed 50 characters'),
            'currency_code.string' => __('Currency code must be a string'),
            'currency_code.max' => __('Currency code must not exceed 10 characters'),
            'capital.string' => __('Capital must be a string'),
            'capital.max' => __('Capital must not exceed 255 characters'),
            'is_enabled.boolean' => __('Is enabled must be true or false'),
        ];
    }
}
