<?php

namespace App\Http\Resources\Admin\SubCategory;

use App\Http\Resources\Admin\Attachment\AttachmentResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defined sub category resource
 */
class SubCategoryResource extends JsonResource
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
            'image' => AttachmentResource::make($this->whenLoaded('image')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
