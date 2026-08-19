<?php

namespace App\Http\Resources\Admin\CertificateProvider;

use App\Http\Resources\Admin\Attachment\AttachmentResource;
use App\Http\Resources\Admin\Certificate\CertificateResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defined certificate provider resource
 */
class CertificateProviderResource extends JsonResource
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
            'website_url' => $this->website_url,
            'is_active' => $this->is_active,
            'logo' => AttachmentResource::make($this->whenLoaded('logo')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'certificates' => CertificateResource::collection($this->whenLoaded('certificates')),
        ];
    }
}
