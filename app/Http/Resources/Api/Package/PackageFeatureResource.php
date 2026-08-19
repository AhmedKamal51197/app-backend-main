<?php

namespace App\Http\Resources\Api\Package;

use App\Http\Resources\Api\Feature\FeatureResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defined package feature resource
 */
class PackageFeatureResource extends JsonResource
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
            'feature' => FeatureResource::make($this->whenLoaded('feature')),
        ];
    }
}
