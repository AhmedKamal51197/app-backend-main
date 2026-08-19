<?php

namespace App\Http\Resources\Admin\Wallet;

use App\Http\Resources\Admin\User\UserMiniResource;
use App\Http\Resources\Api\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defines the deposit resource
 */
class DepositResource extends JsonResource
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
            'user' => UserResource::make($this->whenLoaded('user')),
            'balance_before' => $this->balance_before,
            'credit_amount' => round($this->credit, 2),
            'balance_after' => round($this->balance_after ?? 0, 2),
            'payment_method' => $this->referencable?->payment_gateway ?? null,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
