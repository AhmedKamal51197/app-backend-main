<?php

namespace App\Http\Requests\Admin\Page;

use App\Services\Page\PageContentService;
use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Full save of a legal page from the dashboard: page fields + all sections
 * (a section with an id is updated, without an id is created, missing ones
 * are deleted; the array order is the display order).
 */
class SavePageRequest extends FormRequest
{
    use RequestFailedValidationJsonResponse;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * No HTML in any text and "\n" line endings, before validating.
     */
    protected function prepareForValidation(): void
    {
        $service = new PageContentService();
        $clean = fn ($value) => is_string($value) ? $service->clean($value) : $value;

        $data = [];
        foreach (['title_ar', 'title_en', 'summary_ar', 'summary_en', 'version'] as $field) {
            if ($this->has($field)) {
                $data[$field] = $clean($this->input($field));
            }
        }

        if (is_array($this->input('sections'))) {
            $data['sections'] = array_map(function ($section) use ($clean) {
                if (!is_array($section)) {
                    return $section;
                }
                foreach (['heading_ar', 'heading_en', 'body_ar', 'body_en'] as $field) {
                    if (array_key_exists($field, $section)) {
                        $section[$field] = $clean($section[$field]);
                    }
                }
                if (isset($section['slug']) && is_string($section['slug'])) {
                    $section['slug'] = strtolower(trim($section['slug']));
                }

                return $section;
            }, $this->input('sections'));
        }

        $this->merge($data);
    }

    public function rules(): array
    {
        return [
            'title_ar' => ['required', 'string', 'max:150'],
            'title_en' => ['nullable', 'string', 'max:150'],
            'summary_ar' => ['nullable', 'string', 'max:400'],
            'summary_en' => ['nullable', 'string', 'max:400'],
            'version' => ['nullable', 'string', 'max:20'],
            'en_enabled' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'bump_version' => ['sometimes', 'boolean'],
            'sections' => ['required', 'array', 'min:1', 'max:60'],
            'sections.*.id' => ['nullable', 'string'],
            'sections.*.slug' => ['required', 'string', 'max:60', 'regex:/^[a-z0-9]+(-[a-z0-9]+)*$/', 'distinct'],
            'sections.*.heading_ar' => ['required', 'string', 'max:150'],
            'sections.*.heading_en' => ['nullable', 'string', 'max:150'],
            'sections.*.body_ar' => ['required', 'string', 'max:20000'],
            'sections.*.body_en' => ['nullable', 'string', 'max:20000'],
            'sections.*.is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'title_ar.required' => 'العنوان العربي مطلوب',
            'sections.required' => 'يجب أن تحتوي الصفحة على قسم واحد على الأقل',
            'sections.min' => 'يجب أن تحتوي الصفحة على قسم واحد على الأقل',
            'sections.*.heading_ar.required' => 'القسم رقم :position ينقصه العنوان العربي',
            'sections.*.body_ar.required' => 'القسم رقم :position ينقصه النص العربي',
            'sections.*.slug.required' => 'معرّف القسم رقم :position مطلوب',
            'sections.*.slug.regex' => 'معرّف القسم مكرر أو غير صالح',
            'sections.*.slug.distinct' => 'معرّف القسم مكرر أو غير صالح',
        ];
    }

    /**
     * Section ids must belong to this page; publishing English needs the
     * English title and every visible section translated.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $page = $this->route('page');
            $sections = (array) $this->input('sections', []);
            $ownIds = $page ? $page->sections()->pluck('uuid')->all() : [];

            foreach ($sections as $index => $section) {
                $id = is_array($section) ? ($section['id'] ?? null) : null;
                if ($id && !in_array($id, $ownIds, true)) {
                    $validator->errors()->add("sections.$index.id", 'القسم رقم ' . ($index + 1) . ' لا يتبع هذه الصفحة');
                }
            }

            if (!$this->boolean('en_enabled')) {
                return;
            }

            if (blank($this->input('title_en'))) {
                $validator->errors()->add('title_en', 'لا يمكن نشر النسخة الإنجليزية: العنوان الإنجليزي مطلوب');
            }

            foreach ($sections as $index => $section) {
                if (!is_array($section)) {
                    continue;
                }
                $visible = !array_key_exists('is_active', $section) || filter_var($section['is_active'], FILTER_VALIDATE_BOOLEAN);
                if ($visible && (blank($section['heading_en'] ?? null) || blank($section['body_en'] ?? null))) {
                    $validator->errors()->add("sections.$index", 'لا يمكن نشر النسخة الإنجليزية: القسم ' . ($index + 1) . ' غير مترجم');
                }
            }
        });
    }
}
