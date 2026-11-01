<?php

namespace App\Http\Resources\Api\Skill;

use App\Http\Resources\Admin\Category\CategoryResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defined skill resource
 */
class SkillResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'title' => $this->title(),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'created_at_iso' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'updated_at_iso' => $this->updated_at?->toIso8601String(),
            'category' => CategoryResource::make($this->whenLoaded('category')),
        ];
    }
}
