<?php

namespace App\Http\Resources\Admin\Kyc;

use App\Http\Resources\Admin\Attachment\AttachmentResource;
use App\Http\Resources\Admin\Country\CountryResource;
use App\Http\Resources\Admin\User\UserMiniResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defines kyc resource
 */
class KycResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'country' => CountryResource::make($this->whenLoaded('country')),
            'birth_date' => $this->birth_date?->format('Y-m-d H:i:s'),
            'status' => $this->status,
            'attachments' => AttachmentResource::collection($this->whenLoaded('attachments')),
            'user' => UserMiniResource::make($this->whenLoaded('user')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
