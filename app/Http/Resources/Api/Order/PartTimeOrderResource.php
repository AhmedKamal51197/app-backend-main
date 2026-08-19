<?php

namespace App\Http\Resources\Api\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defines part-time orders resource
 */
class PartTimeOrderResource extends JsonResource
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
            'in_progress' => OrderResource::collection($this->resource['in_progress'] ?? []),
            'previous' => OrderResource::collection($this->resource['previous'] ?? []),
        ];
    }
}
