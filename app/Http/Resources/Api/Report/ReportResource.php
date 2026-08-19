<?php

namespace App\Http\Resources\Api\Report;

use App\Http\Resources\Api\Response\ResponseResource;
use App\Http\Resources\Api\User\UserMiniResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defines report resource
 */
class ReportResource extends JsonResource
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
            'title' => $this->title,
            'body' => $this->body,
            'status' => $this->status->value,
            'user' => UserMiniResource::make($this->whenLoaded('user')),
            'responses_count' => $this->when(isset($this->responses_count), $this->responses_count),
            'latest_response' => ResponseResource::make($this->whenLoaded('latestResponse')),
            'responses' => ResponseResource::collection($this->whenLoaded('responses')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
