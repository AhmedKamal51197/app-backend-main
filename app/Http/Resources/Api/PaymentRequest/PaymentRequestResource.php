<?php

namespace App\Http\Resources\Api\PaymentRequest;

use App\Http\Resources\Api\User\UserMiniResource;
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
            'amount' => $this->amount,
            'fee' => round($this->fee ?? 0, 2),
            'net_amount' => round($this->net_amount ?? $this->amount, 2),
            'status' => $this->status,
            'notes' => $this->notes,
            'user' => UserMiniResource::make($this->whenLoaded('user')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'created_at_iso' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'updated_at_iso' => $this->updated_at?->toIso8601String(),
            'release_at' => $this->release_at?->format('Y-m-d H:i:s'),
            'release_at_iso' => $this->release_at?->toIso8601String(),
        ];
    }
}
