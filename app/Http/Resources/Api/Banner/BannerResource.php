<?php

namespace App\Http\Resources\Api\Banner;

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
            'description' => $this->description(),
            'image' => AttachmentResource::make($this->whenLoaded('attachment')),
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            ];
    }
}
