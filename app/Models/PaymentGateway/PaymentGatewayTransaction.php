<?php

namespace App\Models\PaymentGateway;

use App\Models\Receipt;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A class defines the payment transactions
 */
class PaymentGatewayTransaction extends Model
{
    use HasFactory;
    use HasUuid;
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'transaction_response' => AsCollection::class,
        'amount' => 'double',
    ];

    /**
     * Relation between transaction, checkout
     *
     * @return BelongsTo
     */
    public function checkout(): BelongsTo
    {
        return $this->belongsTo(PaymentGatewayCheckout::class, 'payment_gateway_checkout_id');
    }

    /**
     * Relation between transaction, receipt
     *
     * @return HasOne
     */
    public function receipt(): HasOne
    {
        return $this->hasOne(Receipt::class);
    }
}
