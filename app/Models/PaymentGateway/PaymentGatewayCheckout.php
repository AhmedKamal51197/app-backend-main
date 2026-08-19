<?php

namespace App\Models\PaymentGateway;

use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A class defined for payment gateway checkout model
 */
class PaymentGatewayCheckout extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasUuid;

    protected $guarded = ['id'];

    /**
     * Morph relation to determine from any model the checkout created
     *
     * @return MorphTo
     */
    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Define the relation with user
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
