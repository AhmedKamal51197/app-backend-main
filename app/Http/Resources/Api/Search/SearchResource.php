<?php

namespace App\Http\Resources\Api\Search;

use App\Http\Resources\Api\Service\ServiceResource;
use App\Http\Resources\Api\User\UserMiniResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defines search resource
 */
class SearchResource extends JsonResource
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
            'services' => ServiceResource::collection($this->resource['services'] ?? []),
            'part_time_services' => ServiceResource::collection($this->resource['part_time_services'] ?? []),
            'freelancers' => UserMiniResource::collection($this->resource['freelancers'] ?? []),
        ];
    }
}
