<?php

namespace App\Http\Requests\Api\Search;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class search
 *
 * A class defines to search
 */
class SearchRequest extends FormRequest
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
            'type' => 'nullable|in:one_time,part_time',
            'min' => 'nullable|numeric|min:0',
            'max' => 'nullable|numeric|gte:min',
            'category_id' => 'nullable|exists:categories,uuid',
            'text' => 'nullable|string|max:255',
        ];
    }

    /**
     * Validate
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        if (is_null($this->input('min')) && !is_null($this->input('max'))) {
            $this->merge(['min' => 0]);
        }
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'type.in' => __('Invalid type selected'),
            'min.numeric' => __('Minimum must be a number'),
            'min.min' => __('Minimum must be at least 0'),
            'max.numeric' => __('Maximum must be a number'),
            'max.gte' => __('Maximum must be greater than or equal to minimum'),
            'category_id.exists' => __('Selected category does not exist'),
        ];
    }
}
