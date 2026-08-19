<?php

namespace App\Http\Resources\Api\Portfolio;

use App\Http\Resources\Api\Category\CategoryResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defined portfolio info resource
 */
class PortfolioInfoResource extends JsonResource
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
            'categories' => CategoryResource::collection($this['categories']),
        ];
    }
}
