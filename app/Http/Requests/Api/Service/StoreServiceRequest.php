<?php

namespace App\Http\Requests\Api\Service;

use App\Enums\ServiceTypeEnum;
use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * A class define for the store service
 */
class StoreServiceRequest extends FormRequest
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
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'client_guidelines' => ['required', 'string'],
            'category_id' => ['required', 'string', 'exists:categories,uuid'],
            'sub_category_id' => ['required', 'string', 'exists:sub_categories,uuid'],
            'attachments' => ['required', 'array', 'min:1', 'max:5'],
            'attachments.*' => [
                'file',
                'max:81920'
            ],
            'skill_ids' => ['sometimes', 'array', 'min:1'],
            'skill_ids.*' => ['string', 'exists:skills,uuid'],
            'type' => ['required', Rule::in(ServiceTypeEnum::toArray())],
        ];

        // Add basic package structure rules
        $rules['packages.*.title'] = ['nullable', 'string', 'max:255'];
        $rules['packages.*.price'] = ['required', 'numeric', 'min:1'];
        $rules['packages.*.days'] = ['required', 'numeric', 'min:1'];
        $rules['packages.*.unlimited_revisions'] = ['required', 'boolean'];

        // Conditional revision rule based on unlimited_revisions
        foreach ($this->input('packages', []) as $index => $package) {
            $unlimited = data_get($package, 'unlimited_revisions', false);

            if (!$unlimited) {
                $rules["packages.$index.revisions"] = ['required', 'numeric', 'min:1'];
            } else {
                $rules["packages.$index.revisions"] = ['nullable'];
            }
        }

        // Feature IDs
        $rules['packages.*.feature_ids'] = ['required', 'array', 'min:1'];
        $rules['packages.*.feature_ids.*'] = ['string', 'exists:features,uuid'];

        // Conditional rules for type
        $serviceType = $this->input('type');

        switch ($serviceType) {
            case 'part_time':
                $rules['packages'] = ['required', 'array', 'size:2'];
                break;
            case 'one_time':
                $rules['packages'] = ['required', 'array', 'min:1', 'max:3'];
                break;
            default:
                $rules['packages'] = ['sometimes', 'array', 'min:1'];
                break;
        }

        return $rules;
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
            'client_guidelines.string' => __('Client guidelines must be a string'),
            'category_id.required' => __('Category is required'),
            'category_id.string' => __('Category ID must be a string'),
            'category_id.exists' => __('Selected category does not exist'),
            'sub_category_id.required' => __('Sub category is required'),
            'sub_category_id.string' => __('Sub category ID must be a string'),
            'sub_category_id.exists' => __('Selected sub category does not exist'),
            'attachments.required' => __('Attachments are required'),
            'attachments.array' => __('Attachments must be an array'),
            'attachments.min' => __('At least one attachment is required'),
            'attachments.max' => __('You can upload up to 5 attachments'),
            'attachments.*.file' => __('Each attachment must be a file'),
            'attachments.*.image' => __('Each attachment must be an image'),
            'attachments.*.mimes' => __('Each attachment must be a valid image type (jpeg, jpg, png, gif, svg, webp)'),
            'attachments.*.max' => __('Each attachment must not exceed 8000 kilobytes'),
            'skill_ids.array' => __('Skill IDs must be an array'),
            'skill_ids.min' => __('At least one skill ID is required'),
            'skill_ids.*.string' => __('Each skill ID must be a string'),
            'skill_ids.*.exists' => __('Selected skill does not exist'),
            'type.required' => __('Service type is required'),
            'type.in' => __('Service type must be one of the allowed values'),
            'packages.required' => __('Packages are required'),
            'packages.array' => __('Packages must be an array'),
            'packages.*.title.string' => __('Package title must be a string'),
            'packages.*.title.max' => __('Package title must not exceed 255 characters'),
            'packages.*.price.required' => __('Package price is required'),
            'packages.*.price.numeric' => __('Package price must be a number'),
            'packages.*.price.min' => __('Package price must be at least 1'),
            'packages.*.days.required' => __('Package days are required'),
            'packages.*.days.numeric' => __('Package days must be a number'),
            'packages.*.days.min' => __('Package days must be at least 1'),
            'packages.*.unlimited_revisions.required' => __('Unlimited revisions field is required'),
            'packages.*.unlimited_revisions.boolean' => __('Unlimited revisions must be true or false'),
            'packages.*.revisions.required' => __('Package revisions count is required when unlimited revisions is false'),
            'packages.*.revisions.numeric' => __('Package revisions must be a number'),
            'packages.*.revisions.min' => __('Package revisions must be at least 1'),
            'packages.*.feature_ids.required' => __('Feature IDs are required'),
            'packages.*.feature_ids.array' => __('Feature IDs must be an array'),
            'packages.*.feature_ids.min' => __('At least one feature ID is required'),
            'packages.*.feature_ids.*.string' => __('Each feature ID must be a string'),
            'packages.*.feature_ids.*.exists' => __('Selected feature does not exist'),
            'packages.size' => __('Packages must contain exactly 2 items for part_time service type'),
            'packages.min' => __('Packages must contain at least 1 item for one_time or other service types'),
            'packages.max' => __('Packages can contain up to 3 items for one_time service type'),
        ];
    }

}
