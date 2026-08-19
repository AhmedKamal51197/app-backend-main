<?php

namespace App\Http\Resources\Api\Job;

use App\Http\Resources\Api\Skill\SkillResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defines job skill resource
 */
class JobSkillResource extends JsonResource
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
