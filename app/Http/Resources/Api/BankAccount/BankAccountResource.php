<?php

namespace App\Http\Resources\Api\BankAccount;

use App\Http\Resources\Api\Country\CountryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defined for the bank account resource
 */
class BankAccountResource extends JsonResource
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
            'user_name' => $this->user_name,
            'iban' => $this->iban,
            'swift_code' => $this->swift_code,
            'branch_name' => $this->branch_name,
            'bank_address' => $this->bank_address,
            'user_address' => $this->user_address,
            'country_id' => CountryResource::make($this->whenLoaded('country')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
