<?php

namespace App\Services\Receipt;

use App\Models\PaymentGateway\PaymentGatewayTransaction;
use App\Models\Receipt;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * A class defines the Receipt service
 */
class ReceiptService
{
    /**
     * Create receipt
     *
     * @param User $user
     * @param float $amount
     * @param Model $reference
     * @param PaymentGatewayTransaction|null $transaction
     *
     * @return Receipt
     */
    public static function createReceipt(
        User $user,
        float $amount,
        Model $reference,
        ?PaymentGatewayTransaction $transaction = null
    ): Receipt {

        return Receipt::create([
            'user_id' => $user->getAttribute('id'),
            'amount' => $amount,
            'referenceable_id' => $reference->id,
            'referenceable_type' => get_class($reference),
            'payment_gateway_transaction_id' => $transaction?->id,
        ]);

    }
}
