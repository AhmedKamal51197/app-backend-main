<?php

namespace App\Http\Requests\Api\UserSubCategory;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * A class defined for the store user sub categories
 */
class StoreUserSubCategoriesRequest extends FormRequest
{
    use RequestFailedValidationJsonResponse;
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Define the roles
     *
     * @return array[]
     */
    public function rules(): array
    {
        return [
            'sub_category_ids'   => ['required', 'array'],
            'sub_category_ids.*' => ['uuid', 'exists:sub_categories,uuid'],
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
            'sub_category_ids.required' => __('At least one sub category must be selected'),
            'sub_category_ids.array' => __('Sub categories must be provided as an array'),
            'sub_category_ids.min' => __('At least one sub category must be selected'),
            'sub_category_ids.*.required' => __('Each sub category ID is required'),
            'sub_category_ids.*.string' => __('Each sub category ID must be a string'),
            'sub_category_ids.*.exists' => __('One or more selected sub categories do not exist'),
        ];
    }
}
