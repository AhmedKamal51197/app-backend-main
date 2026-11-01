<?php

namespace App\Http\Resources\Api\Order;

use App\Http\Resources\Api\Attachment\AttachmentResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defines order message resource
 */
class OrderMessageResource extends JsonResource
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
            'text' => $this->message,
            'file' => AttachmentResource::make($this->whenLoaded('file')),
            'is_me' => $this->isMe(),
             'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
             'created_at_iso' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'updated_at_iso' => $this->updated_at?->toIso8601String(),
        ];
    }
}
