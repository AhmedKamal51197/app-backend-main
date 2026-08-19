<?php

namespace App\Http\Resources\Admin\Faq;

use Illuminate\Http\Resources\Json\JsonResource;

class FaqResource extends JsonResource
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
            'id' => $this->uuid,
            'question' => $this->question(),
            'question_ar' => $this->question_ar,
            'question_en' => $this->question_en,
            'answer' => $this->answer(),
            'answer_ar' => $this->answer_ar,
            'answer_en' => $this->answer_en,
            'type' => $this->type,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
