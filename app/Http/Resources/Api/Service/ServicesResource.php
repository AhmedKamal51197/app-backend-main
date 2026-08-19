<?php

namespace App\Http\Resources\Api\Service;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defines the services resource
 */
class ServicesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'one_time' => ServiceResource::collection($this['one_time'] ?? []),
            'part_time' => ServiceResource::collection($this['part_time'] ?? []),
        ];
    }
}
