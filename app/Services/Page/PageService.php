<?php

namespace App\Services\Page;

use App\Models\Page;
use App\Models\Setting;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * A class defines the page service
 */
class PageService
{
    /**
     * Get all pages
     *
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public function index(int $perPage = Setting::PAGE_RESULT_LIMIT): LengthAwarePaginator
    {
        return Page::query()
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Update page
     *
     * @param Page $page
     * @param array $data
     *
     * @return Page
     */
    public function update(Page $page, array $data): Page
    {
        $page->update([
            'content' => $data['content'
            ]]);

        return $page->fresh();
    }
}
