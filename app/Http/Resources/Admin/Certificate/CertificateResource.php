<?php

namespace App\Http\Resources\Admin\Certificate;

use App\Http\Resources\Admin\CertificateProvider\CertificateProviderResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defined certificate resource
 */
class CertificateResource extends JsonResource
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
            'slug' => $this->slug,
            'description' => $this->description,
            'level' => $this->level,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'provider' => CertificateProviderResource::make($this->whenLoaded('provider')),
        ];
    }
}
