<?php

namespace App\Http\Resources\Admin\RefundRequest;

use App\Http\Resources\Admin\User\UserMiniResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defined for the refund request resource, shaped like the withdrawals
 * rows so the dashboard's financial-flow refunds table mirrors the withdrawals
 * table (owner, amount, status, current balance, date).
 */
class RefundRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'amount' => round($this->amount ?? 0, 2),
            'status' => $this->status,
            'reason' => $this->reason,
            // The refund recipient (the order's seeker), set on the model in the service.
            'user' => $this->user ? UserMiniResource::make($this->user) : null,
            'current_balance' => $this->when(isset($this->current_balance), round($this->current_balance ?? 0, 2)),
            'balance_before' => $this->when(isset($this->balance_before), round($this->balance_before ?? 0, 2)),
            'balance_after' => $this->when(isset($this->balance_after), round($this->balance_after ?? 0, 2)),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'released_at' => $this->released_at?->format('Y-m-d H:i:s'),
        ];
    }
}
