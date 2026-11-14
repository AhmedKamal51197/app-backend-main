<?php

namespace App\Services\Page;

use App\Models\Page;
use Carbon\Carbon;

/**
 * Converts between the structured legal pages (summary + sections) and the
 * single block of text that older app versions read from `pages.content`.
 */
class PageContentService
{
    /** "أولاً:" … "عاشرًا:" (both tanween spellings) start a new section. */
    private const SECTION_HEADING = '/^(أول|ثاني|ثالث|رابع|خامس|سادس|سابع|ثامن|تاسع|عاشر)(اً|ًا|ا)\s*:/u';

    /** "1. text:" inside a section becomes a sub-heading. */
    private const SUB_HEADING = '/^(\d+)\.\s*(.+?)\s*:\s*$/u';

    /** Last line of the old texts: "آخر تحديث لهذه السياسة: 25 أغسطس 2025". */
    private const LAST_UPDATE = '/^آخر تحديث[^:]*:\s*(.+)$/u';

    private const ARABIC_MONTHS = [
        'يناير' => 1, 'فبراير' => 2, 'مارس' => 3, 'أبريل' => 4, 'ابريل' => 4, 'إبريل' => 4,
        'مايو' => 5, 'يونيو' => 6, 'يوليو' => 7, 'أغسطس' => 8, 'اغسطس' => 8, 'سبتمبر' => 9,
        'أكتوبر' => 10, 'اكتوبر' => 10, 'نوفمبر' => 11, 'ديسمبر' => 12,
    ];

    /** Stable, meaningful anchors (#who-we-are) for the current legal pages. */
    private const SLUGS = [
        'privacy_policy' => [
            'who-we-are', 'data-we-collect', 'how-we-use-data', 'data-sharing', 'data-protection',
            'your-rights', 'cookies', 'children-privacy', 'policy-updates', 'contact-us',
        ],
        'terms_of_use' => [
            'acceptance', 'service-description', 'registration', 'user-conduct', 'services-and-projects',
            'fees-and-payments', 'account-termination', 'disclaimer', 'terms-changes', 'contact-us',
        ],
    ];

    /** Max length of the summary field; a longer introduction becomes a section. */
    private const SUMMARY_MAX = 400;

    /**
     * Split an old single-text page into summary, sections and publish date.
     *
     * @return array{summary: ?string, sections: array<int, array{slug: string, heading_ar: string, body_ar: string}>, published_at: ?Carbon}
     */
    public function split(string $key, string $content): array
    {
        $intro = [];
        $sections = [];
        $publishedAt = null;
        $current = null;

        foreach (explode("\n", $this->normalize($content)) as $rawLine) {
            $line = trim($rawLine);

            if (preg_match(self::LAST_UPDATE, $line, $match)) {
                $publishedAt = $this->parseArabicDate($match[1]) ?? $publishedAt;
                continue;
            }

            if (preg_match(self::SECTION_HEADING, $line)) {
                if ($current !== null) {
                    $sections[] = $current;
                }
                $current = ['heading_ar' => $line, 'lines' => []];
                continue;
            }

            if ($current === null) {
                $intro[] = $line;
                continue;
            }

            if (preg_match(self::SUB_HEADING, $line, $match)) {
                $line = '### ' . $match[1] . '. ' . $match[2];
            }

            $current['lines'][] = $line;
        }

        if ($current !== null) {
            $sections[] = $current;
        }

        $summary = $this->joinLines($intro);
        $result = [];

        // A long introduction does not fit the summary field: keep it as the first section.
        if ($summary !== null && mb_strlen($summary) > self::SUMMARY_MAX) {
            $result[] = ['slug' => 'introduction', 'heading_ar' => 'مقدمة', 'body_ar' => $summary];
            $summary = null;
        }

        foreach ($sections as $index => $section) {
            $result[] = [
                'slug' => self::SLUGS[$key][$index] ?? 'section-' . ($index + 1),
                'heading_ar' => mb_substr($section['heading_ar'], 0, 150),
                'body_ar' => $this->joinLines($section['lines']) ?? '',
            ];
        }

        return ['summary' => $summary, 'sections' => $result, 'published_at' => $publishedAt];
    }

    /**
     * Rebuild the single-text version (Arabic) for older app versions:
     * summary, then each visible section's heading and plain-text body.
     */
    public function legacyContent(Page $page): string
    {
        $blocks = [];

        if ($page->summary_ar) {
            $blocks[] = $page->summary_ar;
        }

        foreach ($page->sections()->where('is_active', true)->get() as $section) {
            $body = preg_replace('/^###\s*/mu', '', $section->body_ar);
            $body = str_replace('**', '', $body);
            $blocks[] = $section->heading_ar . "\n" . $body;
        }

        if ($page->published_at) {
            $blocks[] = 'آخر تحديث: ' . $this->formatArabicDate($page->published_at);
        }

        return implode("\n\n", $blocks);
    }

    /**
     * Text saved from the dashboard: no HTML, "\n" line endings, trimmed.
     */
    public function clean(?string $text): ?string
    {
        if ($text === null) {
            return null;
        }

        $text = trim($this->normalize(strip_tags($text)));

        return $text === '' ? null : $text;
    }

    private function normalize(string $text): string
    {
        return str_replace(["\r\n", "\r"], "\n", $text);
    }

    /**
     * Join lines, dropping leading/trailing blank lines and collapsing runs of
     * blank lines into one (paragraph separator).
     */
    private function joinLines(array $lines): ?string
    {
        $text = trim(preg_replace("/\n{3,}/", "\n\n", implode("\n", $lines)));

        return $text === '' ? null : $text;
    }

    private function parseArabicDate(string $text): ?Carbon
    {
        $text = strtr(trim($text), ['٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4', '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9']);

        if (!preg_match('/^(\d{1,2})\s+(\S+)\s+(\d{4})/u', $text, $match) || !isset(self::ARABIC_MONTHS[$match[2]])) {
            return null;
        }

        return Carbon::create((int) $match[3], self::ARABIC_MONTHS[$match[2]], (int) $match[1])->startOfDay();
    }

    private function formatArabicDate(Carbon $date): string
    {
        $months = array_flip(['يناير' => 1, 'فبراير' => 2, 'مارس' => 3, 'أبريل' => 4, 'مايو' => 5, 'يونيو' => 6, 'يوليو' => 7, 'أغسطس' => 8, 'سبتمبر' => 9, 'أكتوبر' => 10, 'نوفمبر' => 11, 'ديسمبر' => 12]);

        return $date->day . ' ' . $months[$date->month] . ' ' . $date->year;
    }
}
