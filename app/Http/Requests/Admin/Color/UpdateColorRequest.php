<?php

namespace App\Http\Requests\Admin\Color;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateColorRequest extends FormRequest
{
    use RequestFailedValidationJsonResponse;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name_ar' => 'nullable|string|max:255',
            'name_en' => 'required|string|max:255',
            'hex_code' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'key' => [
                'required',
                'string',
                'max:255',
                Rule::unique('colors', 'key')->ignore($this->color->id),
            ],
            'is_active' => 'boolean',
        ];
    }
}
