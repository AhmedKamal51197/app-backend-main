<?php

namespace App\Http\Resources\Api\Order;

use App\Http\Resources\Api\Attachment\AttachmentResource;
use App\Http\Resources\Api\User\UserMiniResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defines order history resource
 */
class OrderHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'user' => UserMiniResource::make($this->whenLoaded('initiator')),
            'attachments' => AttachmentResource::collection($this->whenLoaded('attachments')),
            'status' => $this->status,
            'details' => $this->details,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
