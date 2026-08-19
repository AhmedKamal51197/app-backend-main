<?php

namespace App\Http\Resources\Admin\Commission;

use App\Http\Resources\Admin\Order\OrderResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defined for the commission resource
 */
class CommissionResource extends JsonResource
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
            'orderable_type' => $this->orderable_type,
            'payable' => OrderResource::make($this->whenLoaded('payable')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
