<?php

namespace App\Http\Resources\Api\UserCertificate;

use App\Http\Resources\Api\Attachment\AttachmentResource;
use App\Http\Resources\Api\Certificate\CertificateResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defines user skill resource
 */
class UserCertificateResource extends JsonResource
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
            'completion_date' => $this->completion_date,
            'expiry_date' => $this->expiry_date,
            'credential_id' => $this->credential_id,
            'file' => AttachmentResource::make($this->whenLoaded('file')),
            'certificate' => CertificateResource::make($this->whenLoaded('certificate')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
