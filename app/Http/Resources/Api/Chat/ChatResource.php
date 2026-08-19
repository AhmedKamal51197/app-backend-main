<?php

namespace App\Http\Resources\Api\Chat;

use App\Http\Resources\Api\Message\MessageResource;
use App\Http\Resources\Api\User\UserMiniResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defined chat resource
 */
class ChatResource extends JsonResource
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
            'type' => $this->type->value,
            'is_active' => $this->is_active,
            'participants' => UserMiniResource::collection($this->whenLoaded('participants')),
            'messages' => MessageResource::collection($this->whenLoaded('messages')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
