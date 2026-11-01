<?php

namespace App\Http\Resources\Api\User;

use App\Http\Resources\Api\Attachment\AttachmentResource;
use App\Http\Resources\Api\UserCategory\UserCategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defines a mini user resource
 */
class UserMiniResource extends JsonResource
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
            'name' => $this->name,
            'status' => $this->status,
            // Other users' emails stay private; only the user sees their own
            'email' => $this->when((int) $this->id === (int) auth()->id(), $this->email),
            'avatar' => AttachmentResource::make($this->whenLoaded('avatar')),
            'role' => $this->getRoleNames()[0],
            'rate' => $this->averageRate(),
            'rates_number' => $this->ratingsCount(),
            'category' => UserCategoryResource::make($this->whenLoaded('category')),
            'last_login_at' => $this->last_login_at?->format('Y-m-d H:i:s'),
            'last_login_at_iso' => $this->last_login_at?->toIso8601String(),
        ];
    }
}
