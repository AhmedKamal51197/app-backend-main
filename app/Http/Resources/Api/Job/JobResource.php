<?php

namespace App\Http\Resources\Api\Job;

use App\Http\Resources\Api\Attachment\AttachmentResource;
use App\Http\Resources\Api\Category\CategoryResource;
use App\Http\Resources\Api\Service\ServiceSkillResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defines the job resource
 */
class JobResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'role' => $this->role,
            'description' => $this->description(),
            'description_ar' => $this->description_ar,
            'description_en' => $this->description_en,
            'weakly_salary' => $this->weakly_salary,
            'monthly_salary' => $this->monthly_salary,
            'working_hours…' => $this->working_hours,
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'skills' => ServiceSkillResource::collection($this->whenLoaded('skills')),
            'attachments' => AttachmentResource::collection($this->whenLoaded('attachments')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
