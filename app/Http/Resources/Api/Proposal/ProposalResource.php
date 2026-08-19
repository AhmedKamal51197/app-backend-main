<?php

namespace App\Http\Resources\Api\Proposal;

use App\Http\Resources\Api\Attachment\AttachmentResource;
use App\Http\Resources\Api\Project\ProjectResource;
use App\Http\Resources\Api\User\UserMiniResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defines the project resource
 */
class ProposalResource extends JsonResource
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
            'description' => $this->description,
            'status' => $this->status,
            'price' => $this->price,
            'time' => $this->time,
            'is_selected' => $this->is_selected,
            'updated_at' => $this->updated_at,
            'user' => UserMiniResource::make($this->whenLoaded('user')),
            'attachments' => AttachmentResource::collection($this->whenLoaded('attachments')),
            'project' => ProjectResource::make($this->whenLoaded('project')),
        ];
    }
}
