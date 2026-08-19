<?php

namespace App\Http\Resources\Api\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defines seeker order resource
 */
class SeekerOrdersResource extends JsonResource
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
            'part_time' => OrderResource::collection($this->resource['part_time'] ?? []),
            'one_time' => OrderResource::collection($this->resource['one_time'] ?? [])
        ];
    }
}
