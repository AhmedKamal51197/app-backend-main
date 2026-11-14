<?php

use App\Models\Page;
use App\Services\Page\PageContentService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * One-time conversion of the existing single-text pages into sections:
 * headings "أولاً:" … "عاشرًا:" start sections, "1. text:" becomes a
 * sub-heading, the text before the first heading becomes the summary and
 * the "آخر تحديث …" line becomes published_at. English stays empty
 * (en_enabled = false) until translated from the dashboard.
 *
 * Also unifies the contact email in the texts to the official inbox.
 */
return new class extends Migration
{
    private const TITLES = [
        'privacy_policy' => ['ar' => 'سياسة الخصوصية', 'en' => 'Privacy Policy'],
        'terms_of_use' => ['ar' => 'شروط الاستخدام', 'en' => 'Terms of Use'],
    ];

    private const OLD_EMAIL = 'support@moaawen.app';
    private const EMAIL = 'support@moawen.sa';

    public function up(): void
    {
        $service = new PageContentService();

        foreach (Page::query()->get() as $page) {
            if ($page->sections()->exists()) {
                continue;
            }

            DB::transaction(function () use ($page, $service) {
                $content = str_replace(self::OLD_EMAIL, self::EMAIL, (string) $page->content);
                $split = $service->split($page->key, $content);

                $page->update([
                    'title_ar' => self::TITLES[$page->key]['ar'] ?? null,
                    'title_en' => self::TITLES[$page->key]['en'] ?? null,
                    'summary_ar' => $split['summary'],
                    'version' => '1.0',
                    'published_at' => $split['published_at'] ?? $page->updated_at,
                    'en_enabled' => false,
                    'content' => $content,
                ]);

                foreach ($split['sections'] as $index => $section) {
                    $page->sections()->create($section + ['sort_order' => $index + 1, 'is_active' => true]);
                }
            });
        }
    }

    public function down(): void
    {
        DB::table('page_sections')->delete();
    }
};
