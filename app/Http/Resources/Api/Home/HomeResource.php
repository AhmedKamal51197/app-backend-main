<?php

namespace App\Http\Resources\Api\Home;

use App\Http\Resources\Api\Category\CategoryResource;
use App\Http\Resources\Api\Service\ServiceResource;
use App\Http\Resources\Api\SubCategory\SubCategoryResource;
use App\Http\Resources\Api\User\UserMiniResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defines home resource
 */
class HomeResource extends JsonResource
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
            'categories' => CategoryResource::collection($this->resource['categories'] ?? []),
            'sub_categories' => SubCategoryResource::collection($this->resource['sub_categories'] ?? []),
            'services' => ServiceResource::collection($this->resource['services'] ?? []),
            'part_time_services' => ServiceResource::collection($this->resource['part_time_services'] ?? []),
            'freelancers' => UserMiniResource::collection($this->resource['freelancers'] ?? []),
        ];
    }
}
