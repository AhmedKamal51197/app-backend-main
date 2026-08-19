<?php

namespace App\Http\Resources\Admin\Banner;

use App\Http\Resources\Api\Attachment\AttachmentResource;
use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
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
            'type' => $this->type,
            'key' => $this->key,
            'title' => $this->title(),
            'title_ar' => $this->title_ar,
            'title_en' => $this->title_en,
            'description' => $this->description(),
            'description_ar' => $this->description_ar,
            'description_en' => $this->description_en,
            'image' => AttachmentResource::make($this->whenLoaded('attachment')),
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            ];
    }
}
