<?php

namespace App\Http\Resources\Api\Attachment;

use App\Enums\AttachmentStorageEnum;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * A class defined attachment resource
 */
class AttachmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'url' => $this->resolveUrl(),
            'type' => $this->type,
            'document_type' => $this->document_type,
            'disk' => $this->disk,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Resolve the attachment URL, falling back to a placeholder when
     * the configured storage (e.g. S3) is not available.
     *
     * @return string|null
     */
    private function resolveUrl(): ?string
    {
        try {
            if ($this->disk === AttachmentStorageEnum::S3->value) {
                if (empty(Config::get('filesystems.disks.s3.region'))) {
                    return asset('images/placeholder.svg');
                }

                return Storage::disk($this->disk)->temporaryUrl($this->path, now()->addHours(24));
            }

            return Storage::disk($this->disk)->url($this->path);
        } catch (Throwable $exception) {
            return asset('images/placeholder.svg');
        }
    }
}