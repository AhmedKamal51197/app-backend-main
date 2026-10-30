<?php

namespace App\Http\Resources\Admin\BankAccount;

use App\Http\Resources\Admin\User\UserMiniResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Admin view of a user's bank account.
 */
class BankAccountResource extends JsonResource
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
            'user' => UserMiniResource::make($this->whenLoaded('user')),
            'user_name' => $this->user_name,
            'iban' => $this->iban,
            'swift_code' => $this->swift_code,
            'bank_name' => $this->bank_name,
            'branch_name' => $this->branch_name,
            'bank_address' => $this->bank_address,
            'user_address' => $this->user_address,
            'country' => $this->whenLoaded('country', fn () => $this->country?->name()),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
