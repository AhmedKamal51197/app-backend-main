<?php

namespace App\Http\Resources\Admin\User;

use App\Http\Resources\Api\Attachment\AttachmentResource;
use App\Http\Resources\Api\BankAccount\BankAccountResource;
use App\Http\Resources\Api\PayPal\PayPalResource;
use App\Http\Resources\Api\UserCategory\UserCategoryResource;
use App\Http\Resources\Api\UserSkillResource\UserSkillResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defines a mini user resource
 */
class UserMiniResource extends JsonResource
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
            'name' => $this->name,
            'status' => $this->status,
            'email' => $this->email,
            'avatar' => AttachmentResource::make($this->whenLoaded('avatar')),
            'role' => $this->getRoleNames()[0],
            'rate' => $this->averageRate(),
            'rates_number' => $this->ratingsCount(),
            'category' => UserCategoryResource::make($this->whenLoaded('category')),
            'paypal' => PayPalResource::make($this->whenLoaded('paypal')),
            'bank_account' => BankAccountResource::make($this->whenLoaded('bankAccount')),
            'skills' => UserSkillResource::collection($this->whenLoaded('userSkills')),
            'balance' => $this->when(isset($this->balance), round($this->balance ?? 0, 2)),
            'withdraw_balance' => $this->when(isset($this->withdraw_balance), round($this->withdraw_balance ?? 0, 2)),
            'pending_balance' => $this->when(isset($this->pending_balance), round($this->pending_balance ?? 0, 2)),
            'spent_balance' => $this->when(isset($this->spent_balance), round($this->spent_balance ?? 0, 2)),
            'orders_count' => $this->when(isset($this->orders_count), $this->orders_count),
            'total_spending' => $this->when(isset($this->total_spending), round($this->total_spending ?? 0, 2)),
            'total_income' => $this->when(isset($this->total_income), round($this->total_income ?? 0, 2)),
            'projects_count' => $this->when(isset($this->projects_count), $this->projects_count),
            'services_count' => $this->when(isset($this->services_count), $this->services_count),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
