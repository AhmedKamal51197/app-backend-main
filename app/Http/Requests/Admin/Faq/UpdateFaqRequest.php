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
            'show_on_landing' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ];
    }

    /**
     * A question shown on the website must have its English text too, since
     * the website shows it in both languages.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $faq = $this->route('faq');
            $showOnWebsite = $this->has('show_on_landing') ? $this->boolean('show_on_landing') : (bool) $faq?->show_on_landing;

            if (!$showOnWebsite) {
                return;
            }

            foreach (['question_en', 'answer_en'] as $field) {
                $value = $this->has($field) ? $this->input($field) : $faq?->{$field};
                if (blank($value)) {
                    $validator->errors()->add($field, __('English text is required for questions shown on the website'));
                }
            }
        });
    }
}
