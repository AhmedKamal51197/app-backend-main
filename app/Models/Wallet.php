<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A class defined for the user wallet
 */
class Wallet extends Model
{
    use HasFactory;
    use HasUuid;
    use SoftDeletes;

    protected $guarded = ['id'];

    /**
     * Get the user associated with the wallet.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the referencable model associated with the wallet.
     */
    public function referencable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the receipt associated with this wallet transaction
     */
    public function receipt(): MorphOne
    {
        return $this->morphOne(Receipt::class, 'referenceable');
    }
}

