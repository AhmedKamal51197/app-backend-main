<?php

namespace App\Http\Requests\Api\UserSubCategory;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * A class defines the remove user sub category request
 */
class RemoveUserSubCategoryRequest extends FormRequest
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
            'sub_category_ids' => 'required|array|min:1',
            'sub_category_ids.*' => 'required|string|exists:sub_categories,uuid',
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
