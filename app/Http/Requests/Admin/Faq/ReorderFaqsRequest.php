<?php

namespace App\Http\Requests\Admin\Faq;

use Illuminate\Foundation\Http\FormRequest;

class ReorderFaqsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'string', 'distinct', 'exists:faqs,uuid'],
        ];
    }
}
