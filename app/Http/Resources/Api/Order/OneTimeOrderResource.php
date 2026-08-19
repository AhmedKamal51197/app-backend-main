<?php

namespace App\Http\Resources\Api\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defines one time orders resource
 */
class OneTimeOrderResource extends JsonResource
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
            'approval_pending' => OrderResource::collection($this->resource['approval_pending'] ?? []),
            'in_progress' => OrderResource::collection($this->resource['in_progress'] ?? []),
            'rejected' => OrderResource::collection($this->resource['rejected'] ?? []),
            'completed' => OrderResource::collection($this->resource['completed'] ?? []),
            'disputed' => OrderResource::collection($this->resource['disputed'] ?? []),
            'refunded' => OrderResource::collection($this->resource['refunded'] ?? []),
            'cancelled' => OrderResource::collection($this->resource['cancelled'] ?? []),
            'revision' => OrderResource::collection($this->resource['revision'] ?? []),
        ];
    }
}
