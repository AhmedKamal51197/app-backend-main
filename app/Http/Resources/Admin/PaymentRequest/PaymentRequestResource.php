<?php

namespace App\Http\Resources\Admin\PaymentRequest;

use App\Http\Resources\Admin\User\UserMiniResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defined for the payment request resource
 */
class PaymentRequestResource extends JsonResource
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
            'amount' => round($this->amount ?? 0, 2),
            'status' => $this->status,
            'notes' => $this->notes,
            'user' => UserMiniResource::make($this->whenLoaded('user')),
            'current_balance' => $this->when(isset($this->current_balance), round($this->current_balance ?? 0, 2)),
            'withdrawal_methods' => $this->when(isset($this->withdrawal_methods), $this->withdrawal_methods ?? []),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'release_at' => $this->release_at?->format('Y-m-d H:i:s'),
        ];
    }
}
