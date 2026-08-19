<?php

namespace App\Http\Resources\Api\Response;

use App\Http\Resources\Api\Attachment\AttachmentResource;
use App\Http\Resources\Api\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defines the response resource
 */
class ResponseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'sender' => UserResource::make($this->whenLoaded('sender')),
            'content' => $this->content,
            'type' => $this->type->value,
            'attachments' => AttachmentResource::collection($this->whenLoaded('attachments')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
