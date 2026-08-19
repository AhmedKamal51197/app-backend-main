<?php

namespace App\Http\Requests\Api\Offer;

use App\Enums\ServiceTypeEnum;
use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * A class define for the store offer
 */
class StoreOfferRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'category_id' => ['required', 'string', 'exists:categories,uuid'],
            'sub_category_id' => ['required', 'string', 'exists:sub_categories,uuid'],
            'type' => ['required', Rule::in(ServiceTypeEnum::toArray())],
            'revisions' => ['required', 'numeric', 'min:1'],
            'price' => ['required', 'numeric', 'min:10'],
            'time' => ['required', 'numeric', 'min:1'],
            'feature_ids' => ['nullable', 'array'],
            'feature_ids.*' => ['string', 'exists:features,uuid']
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
            'title.required' => __('Title is required'),
            'title.string' => __('Title must be a string'),
            'title.max' => __('Title must not exceed 255 characters'),
            'description.required' => __('Description is required'),
            'description.string' => __('Description must be a string'),
            'category_id.required' => __('Category is required'),
            'category_id.string' => __('Category ID must be a string'),
            'category_id.exists' => __('Selected category does not exist'),
            'sub_category_id.required' => __('Sub category is required'),
            'sub_category_id.string' => __('Sub category ID must be a string'),
            'sub_category_id.exists' => __('Selected sub category does not exist'),
            'type.required' => __('Service type is required'),
            'type.in' => __('Service type must be one of the allowed values'),
            'revisions.required' => __('Revisions are required'),
            'revisions.numeric' => __('Revisions must be a number'),
            'revisions.min' => __('Revisions must be at least 1'),
            'price.required' => __('Price is required'),
            'price.numeric' => __('Price must be a number'),
            'price.min' => __('Price must be at least 10'),
            'time.min' => __('Time must be at least 10'),
        ];
    }
}
