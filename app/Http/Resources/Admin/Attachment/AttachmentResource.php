<?php

namespace App\Http\Resources\Admin\Attachment;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

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
            'url' => Storage::disk($this->disk)->temporaryUrl($this->path, now()->addHours(24)),
            'type' => $this->type,
            'document_type' => $this->document_type,
            'disk' => $this->disk,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
