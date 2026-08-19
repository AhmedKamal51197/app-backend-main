<?php

namespace App\Http\Resources\Admin\DashboardNotification;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defines the dashboard notification resource
 */
class DashboardNotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'title' => $this->title,
            'details' => $this->details,
            'type' => $this->type,
            'resolved' => $this->resolved,
            'is_seen' => $this->is_seen,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
