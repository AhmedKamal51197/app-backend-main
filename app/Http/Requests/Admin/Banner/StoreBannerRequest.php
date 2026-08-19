<?php

namespace App\Http\Requests\Admin\Banner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'in:main,category'],
            'key' => ['required', 'string', 'max:255', Rule::unique('banners', 'key')],
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'description_ar' => ['required', 'string'],
            'description_en' => ['nullable', 'string'],
            'attachment' => ['nullable', 'file', 'image', 'max:5120'],
            ];
    }
}
