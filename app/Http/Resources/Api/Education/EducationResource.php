<?php

namespace App\Http\Resources\Api\Education;

use App\Http\Resources\Api\Attachment\AttachmentResource;
use App\Http\Resources\Api\EducationDegree\EducationDegreeResource;
use App\Http\Resources\Api\EducationMajor\EducationMajorResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defined education resource
 */
class EducationResource extends JsonResource
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
            'graduation_year' => $this->graduation_year,
            'major' => $this->major,
            'degree' => EducationDegreeResource::make($this->whenLoaded('educationDegree')),
            'image' => AttachmentResource::make($this->whenLoaded('image')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
