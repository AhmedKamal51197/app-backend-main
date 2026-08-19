<?php

namespace App\Http\Resources\Api\Wallet;

use App\Http\Resources\Api\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defines Wallet resource
 */
class WalletResource extends JsonResource
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
            'credit' => (float) $this->credit,
            'debit' => (float) $this->debit,
            'balance' => (float) $this->balance,
            'user' => UserResource::make($this->whenLoaded('user')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
