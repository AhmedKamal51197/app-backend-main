<?php

namespace App\Http\Resources\Api\Feature;

use App\Http\Resources\Api\Category\CategoryResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defined feature resource
 */
class FeatureResource extends JsonResource
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
            'title' => $this->title(),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'category' => CategoryResource::make($this->whenLoaded('category')),
        ];
    }
}
