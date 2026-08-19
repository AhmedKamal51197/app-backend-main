<?php

namespace App\Http\Resources\Api\Message;

use App\Http\Resources\Api\Attachment\AttachmentResource;
use App\Http\Resources\Api\User\UserMiniResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defined message resource
 */
class MessageResource extends JsonResource
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
            'content' => $this->content,
            'type' => $this->type->value,
            'sender' => UserMiniResource::make($this->whenLoaded('sender')),
            'attachments' => AttachmentResource::collection($this->whenLoaded('attachments')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
