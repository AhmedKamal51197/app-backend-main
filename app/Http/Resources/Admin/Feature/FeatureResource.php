<?php

namespace App\Http\Resources\Admin\Feature;

use App\Http\Resources\Admin\Category\CategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defines the feature resource
 */
class FeatureResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'title' => $this->title(),
            'title_ar' => $this->title_ar,
            'title_en' => $this->title_en,
            'category' => CategoryResource::make($this->whenLoaded('category')),

        ];
    }
}
