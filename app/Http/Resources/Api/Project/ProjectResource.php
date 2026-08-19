<?php

namespace App\Http\Resources\Api\Project;

use App\Http\Resources\Api\Attachment\AttachmentResource;
use App\Http\Resources\Api\Category\CategoryResource;
use App\Http\Resources\Api\Proposal\ProposalResource;
use App\Http\Resources\Api\Rate\RateResource;
use App\Http\Resources\Api\SubCategory\SubCategoryResource;
use App\Http\Resources\Api\User\UserMiniResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defines the project resource
 */
class ProjectResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'min_price' => $this->min_price,
            'max_price' => $this->max_price,
            'time' => $this->time,
            'cancellation_reason' => $this->cancellation_reason,
            'cancelled_at' => $this->cancelled_at,
            'completed_at' => $this->completed_at,
            'started_at' => $this->started_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'sub_category' => SubCategoryResource::make($this->whenLoaded('subCategory')),
            'user' => UserMiniResource::make($this->whenLoaded('user')),
            'attachments' => AttachmentResource::collection($this->whenLoaded('attachments')),
            'selected_proposal' =>ProposalResource::make($this->whenLoaded('selectedProposal')),
            'proposals' => ProposalResource::collection($this->whenLoaded('proposals')),
            'proposals_count' => $this->proposals_count ?? $this->whenLoaded('proposals', fn() => $this->proposals->count()),
            'rates' => RateResource::collection($this->whenLoaded('rates')),
        ];
    }
}
