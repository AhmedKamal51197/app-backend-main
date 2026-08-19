<?php

namespace App\Http\Resources\Api\Experience;

use App\Http\Resources\Api\Attachment\AttachmentResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defined experience resource
 */
class ExperienceResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'company' => $this->company,
            'employment_type' => $this->employment_type,
            'is_current' => $this->is_current,
            'image' => AttachmentResource::make($this->whenLoaded('image')),
            'start_date' => $this->start_date?->format('Y-m-d H:i:s'),
            'end_date' => $this->end_date?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
