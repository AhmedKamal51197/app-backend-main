<?php

namespace App\Http\Resources\Api\Education;

use App\Http\Resources\Api\EducationMajor\EducationMajorResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defined education info resource
 */
class EducationInfo extends JsonResource
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
            'majors' => EducationMajorResource::collection($this['majors']),
            'degrees' => EducationMajorResource::collection($this['degrees']),
        ];
    }
}
