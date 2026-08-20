<?php

namespace App\Http\Resources\Admin\Project;

use App\Http\Resources\Admin\Category\CategoryResource;
use App\Http\Resources\Admin\Chat\ChatResource;
use App\Http\Resources\Admin\SubCategory\SubCategoryResource;
use App\Http\Resources\Admin\User\UserMiniResource;
use App\Http\Resources\Api\Attachment\AttachmentResource;
use App\Http\Resources\Api\Proposal\ProposalResource;
use App\Http\Resources\Api\Rate\RateResource;
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
            'min_price' => $this->min_price,
            'max_price' => $this->max_price,
            'time' => $this->time,
            'status' => $this->status,
            'is_approved' => $this->is_approved,
            'start_date' => $this->start_date?->format('Y-m-d H:i:s'),
            'end_date' => $this->end_date?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'user' => UserMiniResource::make($this->whenLoaded('user')),
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'sub_category' => SubCategoryResource::make($this->whenLoaded('subCategory')),
            'attachments' => AttachmentResource::collection($this->whenLoaded('attachments')),
            'selected_proposal' => ProposalResource::make($this->whenLoaded('selectedProposal')),
            'proposals' => ProposalResource::collection($this->whenLoaded('proposals')),
            'proposals_count' => $this->proposals_count ?? $this->whenLoaded('proposals', fn() => $this->proposals->count()),
            'cancellation_reason' => $this->cancellation_reason,
            'cancelled_at' => $this->cancelled_at?->format('Y-m-d H:i:s'),
            'completed_at' => $this->completed_at?->format('Y-m-d H:i:s'),
            'started_at' => $this->started_at?->format('Y-m-d H:i:s'),
            'rates' => RateResource::Collection($this->whenLoaded('rates')),
            'chat_with_selected_proposal' => $this->when(
                $this->relationLoaded('chatWithSelectedProposal') && $this->chatWithSelectedProposal,
                fn() => ChatResource::make($this->chatWithSelectedProposal)
            ),
        ];
    }
}
