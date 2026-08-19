<?php

namespace App\Http\Requests\Admin\Feature;

use Illuminate\Foundation\Http\FormRequest;

/**
 * A class defines the request to update feature
 */
class UpdateFeatureRequest extends FormRequest
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
            'category_id' => ['sometimes', 'exists:categories,uuid'],
            'title_ar' => ['sometimes', 'string', 'max:255'],
            'title_en' => ['sometimes', 'string', 'max:255'],
        ];
    }
}
