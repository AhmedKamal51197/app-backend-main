<?php

namespace App\Http\Resources\Api\Rate;

use App\Http\Resources\Api\Order\OrderResource;
use App\Http\Resources\Api\User\UserMiniResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defines rate resource
 */
class RateResource extends JsonResource
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
            'comment' => $this->comment,
            'rate' => $this->rate,
            'user' => UserMiniResource::make($this->whenLoaded('user')),
            'rated_user' => UserMiniResource::make($this->whenLoaded('ratedUser')),
            'order' => OrderResource::make($this->whenLoaded('order')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
