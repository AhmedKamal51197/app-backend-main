<?php

namespace App\Http\Requests\Admin\Banner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $bannerId = $this->route('banner')->id;

        return [
            'key' => ['sometimes', 'string', 'max:255', Rule::unique('banners', 'key')->ignore($bannerId)],
            'title_ar' => ['sometimes', 'string', 'max:255'],
            'title_en' => ['sometimes', 'string', 'max:255'],
            'description_ar' => ['sometimes', 'string'],
            'description_en' => ['sometimes', 'string'],
            'attachment' => ['nullable', 'file', 'image', 'max:5120'],
        ];
    }
}
