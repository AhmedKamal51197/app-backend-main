<?php

namespace App\Http\Resources\Admin\Page;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'key' => $this->key,
            'title_ar' => $this->title_ar,
            'title_en' => $this->title_en,
            'summary_ar' => $this->summary_ar,
            'summary_en' => $this->summary_en,
            'version' => $this->version,
            'published_at' => $this->published_at?->toIso8601String(),
            'en_enabled' => (bool) $this->en_enabled,
            'sections' => $this->whenLoaded('sections', fn () => $this->sections->map(fn ($section) => [
                'id' => $section->uuid,
                'slug' => $section->slug,
                'sort_order' => $section->sort_order,
                'heading_ar' => $section->heading_ar,
                'heading_en' => $section->heading_en,
                'body_ar' => $section->body_ar,
                'body_en' => $section->body_en,
                'is_active' => $section->is_active,
            ])->all()),
            'content' => $this->content,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'created_at_iso' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'updated_at_iso' => $this->updated_at?->toIso8601String(),
        ];
    }
}
