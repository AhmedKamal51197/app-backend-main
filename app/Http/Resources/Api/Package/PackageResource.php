<?php

namespace App\Http\Resources\Api\Package;

use App\Http\Resources\Admin\Order\OrderResource;
use App\Http\Resources\Api\Service\ServiceResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defined package resource
 */
class PackageResource extends JsonResource
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
            'title' => $this->title,
            'price' => $this->price,
            'days' => $this->days,
            'revisions' => $this->revisions,
            'unlimited_revisions' => $this->unlimited_revisions,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'features' => PackageFeatureResource::collection($this->whenLoaded('features')),
            'service' => ServiceResource::make($this->whenLoaded('service')),
            'orders' => OrderResource::collection($this->whenLoaded('orders')),
        ];
    }
}
