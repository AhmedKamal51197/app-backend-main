<?php

namespace App\Http\Requests\Admin\Faq;

use App\Enums\UserTypesEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFaqRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question_ar' => ['sometimes', 'string', 'max:255'],
            'question_en' => ['sometimes', 'string', 'max:255'],
            'answer_ar' => ['sometimes', 'string'],
            'answer_en' => ['sometimes', 'string'],
            'type' => ['sometimes', 'nullable', Rule::in(UserTypesEnum::toArray())],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
