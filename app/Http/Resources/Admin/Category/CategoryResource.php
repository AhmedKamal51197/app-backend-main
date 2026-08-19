<?php

namespace App\Http\Resources\Admin\Category;

use App\Http\Resources\Admin\Attachment\AttachmentResource;
use App\Http\Resources\Admin\Skill\SkillResource;
use App\Http\Resources\Admin\SubCategory\SubCategoryResource;
use App\Http\Resources\Api\Feature\FeatureResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defined category resource
 */
class CategoryResource extends JsonResource
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
            'title_ar' => $this->title_ar,
            'title_en' => $this->title_en,
            'is_enabled' => $this->is_enabled,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'image' => AttachmentResource::make($this->whenLoaded('image')),
            'sub_categories' => SubCategoryResource::collection($this->whenLoaded('subCategories')),
            'skills' => SkillResource::collection($this->whenLoaded('skills')),
            'features' => FeatureResource::collection($this->whenLoaded('features')),
        ];
    }
}
