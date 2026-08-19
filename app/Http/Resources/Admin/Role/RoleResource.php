<?php

namespace App\Http\Resources\Admin\Role;

use App\Http\Resources\Admin\Permission\PermissionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defines role resource
 */
class RoleResource extends JsonResource
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
            'name_ar' => $this->name_ar,
            'allowed_user' => $this->allowed_user,
            'is_active' => $this->is_active,
            'description' => $this->description,
            'type' => $this->type,
            'access_level' => $this->access_level,
            'permissions' => PermissionResource::collection($this->whenLoaded('permissions')),
        ];
    }
}
