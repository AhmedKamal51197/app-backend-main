<?php

namespace App\Http\Resources\Api\Admin\NotificationSetting;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defines a notification setting resource
 */
class NotificationSettingResource extends JsonResource
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
            'setting_key' => $this->setting_key,
            'setting_name_en' => $this->setting_name_en,
            'setting_name_ar' => $this->setting_name_ar,
            'setting_name' => $this->setting_name(),
            'description_en' => $this->description_en,
            'description_ar' => $this->description_ar,
            'description' => $this->description(),
            'is_enabled' => $this->is_enabled,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
