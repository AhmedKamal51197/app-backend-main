<?php

namespace App\Http\Resources\Api\UserSkillResource;

use App\Http\Resources\Api\Skill\SkillResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defines user skill resource
 */
class UserSkillResource extends JsonResource
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
            'skill' => SkillResource::make($this->whenLoaded('skill')),
        ];
    }
}
