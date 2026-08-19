<?php

namespace App\Http\Resources\Api\Category;

use App\Http\Resources\Api\Attachment\AttachmentResource;
use App\Http\Resources\Api\SubCategory\SubCategoryResource;
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
            'is_enabled' => $this->is_enabled,
            'image' => AttachmentResource::make($this->whenLoaded('image')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'sub_categories' => SubCategoryResource::collection($this->whenLoaded('subCategories')),
        ];
    }
}
