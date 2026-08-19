<?php

namespace App\Http\Resources\Admin\Chat;

use App\Http\Resources\Admin\Order\OrderResource;
use App\Http\Resources\Admin\Project\ProjectResource;
use App\Http\Resources\Admin\Service\ServiceResource;
use App\Http\Resources\Api\Message\MessageResource;
use App\Http\Resources\Api\User\UserMiniResource;
use App\Models\Project;
use App\Models\Service;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defined admin chat list resource (minimal data)
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
            'title' => $this->title,
            'chattable' => $this->getChattableResource(),
            'order' =>  OrderResource::make($this->whenLoaded('order')),
            'participants' => UserMiniResource::collection($this->whenLoaded('participants')),
            'messages' => MessageResource::collection($this->whenLoaded('messages')),
            'last_message_at' => $this->last_message_at?->format('Y-m-d H:i:s'),
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Get chattable resource based on type
     *
     * @return mixed
     */
    private function getChattableResource()
    {
        if (!$this->chattable) {
            return null;
        }

        return match($this->type->value) {
            'project' => $this->chattable instanceof Project
                ? ProjectResource::make($this->chattable)
                : null,
            'service' => $this->chattable instanceof Service
                ? ServiceResource::make($this->chattable)
                : null,
            default => null,
        };
    }
}
