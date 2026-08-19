<?php

namespace App\Http\Resources\Api\Favorite;

use App\Http\Resources\Api\Service\ServiceResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defines favorite service resource
 */
class FavoriteServiceResource extends JsonResource
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
            'one_time' => ServiceResource::collection($this['one_time'] ?? []),
            'part_time' => ServiceResource::collection($this['part_time'] ?? []),        ];
    }
}
