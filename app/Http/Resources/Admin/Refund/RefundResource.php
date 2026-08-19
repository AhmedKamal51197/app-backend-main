<?php

namespace App\Http\Resources\Admin\Refund;

use App\Http\Resources\Admin\Order\OrderResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defined for the refund resource
 */
class RefundResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'title' => $this->title,
            'amount' => $this->amount,
            'refundable' => OrderResource::make($this->whenLoaded('refundable')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
