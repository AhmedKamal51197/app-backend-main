<?php

namespace App\Services\Faq;

use App\Events\LogExceptionEvent;
use App\Models\Faq;
use Exception;
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
    public function index(int $perPage, string $type, bool $onlyActive = false): LengthAwarePaginator
    {
        $query = Faq::query();

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
            ]);

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
            ]);

            return $faq->fresh();
    }

    /**
     * Delete FAQ
     *
     * @param Faq $faq
     *
     * @return bool
     */
    public function delete(Faq $faq): bool
    {
        return $faq->delete();
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
        return $faq->fresh();
    }
}
