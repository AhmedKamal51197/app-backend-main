<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A class defines the order message model with relations
 */
class OrderMessage extends Model
{
    use HasUuid;
    use SoftDeletes;

    /**
     * Guarded (id)
     *
     * @var string[]
     */
    protected $guarded = ['id'];

    /**
     * Define the relation with user
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define the relation with orders
     *
     * @return BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Define if i am the sender
     *
     * @return bool
     */
    public function isMe(): bool
    {
        return request()->user()->id === $this->user_id;
    }

    /**
     * Define the relation with file
     *
     * @return MorphOne
     */
    public function file(): MorphOne
    {
        return $this->morphOne(Attachment::class, 'attachable');
    }
}
