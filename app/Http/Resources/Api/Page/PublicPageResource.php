<?php

namespace App\Http\Resources\Api\Page;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Public legal page in the requested language, with Arabic fallback while the
 * English version is not published (en_enabled = false).
 *
 * `content` is the old single-text Arabic version, kept for app versions that
 * still read it.
 */
class PublicPageResource extends JsonResource
{
    /**
     * Language actually returned for the requested one.
     */
    public static function languageFor(string $requested, bool $enEnabled): string
    {
        return $requested === 'en' && $enEnabled ? 'en' : 'ar';
    }

    public function toArray($request): array
    {
        $requested = app()->getLocale();
        $lang = self::languageFor($requested, (bool) $this->en_enabled);
        $sections = $this->relationLoaded('sections') ? $this->sections->where('is_active', true)->values() : collect();

        return [
            'id' => $this->uuid,
            'key' => $this->key,
            'lang' => $lang,
            'fallback' => $lang !== $requested,
            'title' => $this->{"title_$lang"},
            'summary' => $this->{"summary_$lang"},
            'version' => $this->version,
            'published_at' => $this->published_at?->toIso8601String(),
            'sections' => $sections->map(fn ($section) => [
                'slug' => $section->slug,
                'heading' => $section->{"heading_$lang"},
                'body' => $section->{"body_$lang"},
            ])->all(),
            'content' => $this->content,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'created_at_iso' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'updated_at_iso' => $this->updated_at?->toIso8601String(),
        ];
    }
}
