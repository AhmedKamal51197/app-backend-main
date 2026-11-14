<?php

namespace App\Services\Faq;

use App\Events\LogExceptionEvent;
use App\Jobs\NotifyWebsiteContentChanged;
use App\Models\Faq;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class FaqService
{
    /**
     * Get all FAQs with filters
     *
     * @param int $perPage
     * @param string $type
     * @param bool $onlyActive
     *
     * @return LengthAwarePaginator
     */
    /**
     * Active FAQs shown on the website, in display order (no pagination: a
     * handful of questions).
     */
    public function website(): Collection
    {
        return Faq::query()
            ->where('is_active', true)
            ->where('show_on_landing', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    public function index(int $perPage, string $type, bool $onlyActive = false): LengthAwarePaginator
    {
        $query = Faq::query()->orderBy('sort_order')->orderBy('id');

        if ($type) {
            $query->where('type', $type);
        }

        if ($onlyActive) {
            $query->where('is_active', true);
        }

        return $query->paginate($perPage);
    }

    /**
     * Create a new FAQ
     *
     * @param array $data
     *
     * @return Faq
     *
     * @throws Exception
     */
    public function create(array $data): Faq
    {
            $faq = Faq::create([
                'question_ar' => $data['question_ar'],
                'question_en' => $data['question_en'] ?? null,
                'answer_ar' => $data['answer_ar'],
                'answer_en' => $data['answer_en'] ?? null,
                'type' => $data['type'] ?? null,
                'is_active' => $data['is_active'] ?? true,
                'show_on_landing' => $data['show_on_landing'] ?? false,
                'sort_order' => $data['sort_order'] ?? (int) Faq::max('sort_order') + 1,
            ]);

            NotifyWebsiteContentChanged::send('faqs');

            return $faq->fresh();
    }

    /**
     * Update FAQ
     *
     * @param Faq $faq
     * @param array $data
     *
     * @return Faq
     *
     * @throws Exception
     */
    public function update(Faq $faq, array $data): Faq
    {
            $faq->update([
                'question_ar' => $data['question_ar'] ?? $faq->question_ar,
                'question_en' => $data['question_en'] ?? $faq->question_en,
                'answer_ar' => $data['answer_ar'] ?? $faq->answer_ar,
                'answer_en' => $data['answer_en'] ?? $faq->answer_en,
                'type' => $data['type'] ?? $faq->type,
                'is_active' => $data['is_active'] ?? $faq->is_active,
                'show_on_landing' => $data['show_on_landing'] ?? $faq->show_on_landing,
                'sort_order' => $data['sort_order'] ?? $faq->sort_order,
            ]);

            NotifyWebsiteContentChanged::send('faqs');

            return $faq->fresh();
    }

    /**
     * Delete FAQ
     *
     * @param Faq $faq
     *
     * @return bool
     */
    /**
     * Save a new display order (dashboard drag & drop): ids in their new order.
     */
    public function reorder(array $ids): void
    {
        DB::transaction(function () use ($ids) {
            foreach (array_values($ids) as $index => $uuid) {
                Faq::where('uuid', $uuid)->update(['sort_order' => $index + 1]);
            }
        });

        NotifyWebsiteContentChanged::send('faqs');
    }

    public function delete(Faq $faq): bool
    {
        $deleted = $faq->delete();
        NotifyWebsiteContentChanged::send('faqs');

        return $deleted;
    }

    /**
     * Toggle FAQ active status
     *
     * @param Faq $faq
     *
     * @return Faq
     */
    public function toggleActive(Faq $faq): Faq
    {
        $faq->update(['is_active' => !$faq->is_active]);
        NotifyWebsiteContentChanged::send('faqs');

        return $faq->fresh();
    }
}
