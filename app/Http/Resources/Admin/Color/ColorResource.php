<?php

namespace App\Http\Resources\Admin\Color;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ColorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'name' => $this->name(),
            'hex_code' => $this->hex_code,
            'key' => $this->key,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
