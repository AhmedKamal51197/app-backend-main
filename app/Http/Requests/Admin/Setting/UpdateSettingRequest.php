<?php

namespace App\Http\Requests\Admin\Setting;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * A class defines the request to update setting
 */
class UpdateSettingRequest extends FormRequest
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
            'setting_value' => ['required_if:type,text', 'string'],
            'type' => ['required', Rule::in(['text', 'attachment'])],
            'attachment' => ['required_if:type,attachment', 'file', 'max:10240'], // 10MB max
        ];
    }
}
