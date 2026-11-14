<?php

namespace App\Services\Page;

use App\Jobs\NotifyWebsiteContentChanged;
use App\Models\Page;
use App\Models\Setting;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * A class defines the page service
 */
class PageService
{
    public function __construct(private readonly PageContentService $content)
    {
    }

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

        NotifyWebsiteContentChanged::send($page->key);

        return $page->fresh();
    }

    /**
     * Save a page with all its sections in one transaction: sections with an
     * id are updated, without an id created, and the ones not sent deleted.
     * The array order becomes the display order. `bump_version` marks a
     * substantive change: the visible "last updated" date moves to now and the
     * version goes up (1.1 -> 1.2); without it the save is a quiet correction.
     *
     * @param Page $page
     * @param array $data
     *
     * @return Page
     */
    public function save(Page $page, array $data): Page
    {
        DB::transaction(function () use ($page, $data) {
            $attributes = [
                'title_ar' => $data['title_ar'],
                'title_en' => $data['title_en'] ?? null,
                'summary_ar' => $data['summary_ar'] ?? null,
                'summary_en' => $data['summary_en'] ?? null,
                'version' => $data['version'] ?? $page->version ?? '1.0',
                'en_enabled' => $data['en_enabled'] ?? $page->en_enabled,
                'is_active' => $data['is_active'] ?? $page->is_active,
            ];

            if (!empty($data['bump_version'])) {
                $attributes['version'] = $this->nextVersion($attributes['version']);
                $attributes['published_at'] = now();
            }

            $page->update($attributes);

            $existing = $page->sections()->get()->keyBy('uuid');
            $keep = collect($data['sections'])->pluck('id')->filter()->all();

            $page->sections()->whereNotIn('uuid', $keep)->delete();

            // Free the slugs first so sections can swap slugs without hitting
            // the unique (page_id, slug) index mid-save.
            foreach ($existing->only($keep) as $section) {
                $section->update(['slug' => 'tmp-' . $section->id]);
            }

            foreach (array_values($data['sections']) as $index => $input) {
                $values = [
                    'slug' => $input['slug'],
                    'sort_order' => $index + 1,
                    'heading_ar' => $input['heading_ar'],
                    'heading_en' => $input['heading_en'] ?? null,
                    'body_ar' => $input['body_ar'],
                    'body_en' => $input['body_en'] ?? null,
                    'is_active' => $input['is_active'] ?? true,
                ];

                $section = !empty($input['id']) ? $existing->get($input['id']) : null;
                $section ? $section->update($values) : $page->sections()->create($values);
            }

            // Older app versions read one block of Arabic text.
            $page->update(['content' => $this->content->legacyContent($page->fresh())]);
        });

        NotifyWebsiteContentChanged::send($page->key);

        return $page->fresh('sections');
    }

    /**
     * "1.1" -> "1.2", "2" -> "2.1".
     */
    private function nextVersion(string $version): string
    {
        $parts = explode('.', $version);

        if (count($parts) === 1 || !is_numeric(end($parts))) {
            return $version . '.1';
        }

        $parts[count($parts) - 1] = (string) ((int) end($parts) + 1);

        return implode('.', $parts);
    }
}
