<?php

namespace App\Http\Resources\Api\UserSubCategory;

use App\Http\Resources\Admin\SubCategory\SubCategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defines user sub category resource
 */
class UserSubCategoryResource extends JsonResource
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
            'id' => $this->uuid,
            'sub_category' => SubCategoryResource::make($this->whenLoaded('subCategory')),
        ];
    }
}
