<?php

namespace App\Http\Resources\Api\SubCategory;

use App\Http\Resources\Api\Attachment\AttachmentResource;
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
            'image' => AttachmentResource::make($this->whenLoaded('image')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
