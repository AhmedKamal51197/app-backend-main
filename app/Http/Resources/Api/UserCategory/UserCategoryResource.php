<?php

namespace App\Http\Resources\Api\UserCategory;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defines user category resource
 */
class UserCategoryResource extends JsonResource
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
            'title' => $this->title(),
            'title_ar' => $this->title_ar,
            'title_en' => $this->title_en,
        ];
    }
}
