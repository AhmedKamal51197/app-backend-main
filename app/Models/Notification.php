<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A class defines the notification model with relations
 */
class Notification extends Model
{
    use HasUuid;
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'important' => 'bool',
        'seen' => 'bool',
    ];

    /**
     * Define the user relation
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
