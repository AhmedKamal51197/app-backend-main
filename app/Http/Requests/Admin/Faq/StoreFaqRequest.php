<?php

namespace App\Http\Requests\Admin\Faq;

use App\Enums\UserTypesEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFaqRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question_ar' => ['required', 'string', 'max:255'],
            'question_en' => ['nullable', 'required_if:show_on_landing,true,1', 'string', 'max:255'],
            'answer_ar' => ['required', 'string'],
            'answer_en' => ['nullable', 'required_if:show_on_landing,true,1', 'string'],
            'type' => ['nullable', Rule::in(UserTypesEnum::toArray())],
            'is_active' => ['nullable', 'boolean'],
            'show_on_landing' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
