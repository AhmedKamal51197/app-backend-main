<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A class defines the order model with relations
 */
class Order extends Model
{
    use HasUuid;
    use SoftDeletes;

    protected $casts = [
        'main_order' => 'boolean',
        'approved_at' => 'datetime',
        'released_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];
    /**
     * Guarded (id)
     *
     * @var string[]
     */
    protected $guarded = ['id'];

    /**
     * Define the ordarable
     *
     * @return MorphTo
     */
    public function orderable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Define the seeker relation
     *
     * @return BelongsTo
     */
    public function seeker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seeker_id');
    }

    /**
     * Define the provider relation
     *
     * @return BelongsTo
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    /**
     * Adding booted function
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function (Order $order) {
            // Set time from orderable if available
            if ($order->orderable && $order->orderable->days) {
                $order->time = $order->orderable->days;
            }

            // Generate code like #ORD92-SK
            $order->code = self::generateCode();
        });
    }

    /**
     * Generate a custom code for the order
     *
     * @return string
     */
    protected static function generateCode(): string
    {
        $number = rand(10, 99);
        $suffix = strtoupper(substr(bin2hex(random_bytes(1)), 0, 6));

        return "#ORD{$number}-{$suffix}";
    }

    /**
     * Public function to define the relation with attachments
     *
     * @return MorphMany
     */
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Get the histories for the order.
     *
     * @return HasMany
     */
    public function histories(): HasMany
    {
        return $this->hasMany(OrderHistory::class);
    }

    /**
     * Define the relation with messages
     *
     * @return HasMany
     */
    public function messages(): HasMany
    {
        return $this->hasMany(OrderMessage::class);
    }

    /**
     * Define the orders has many renewals
     *
     * @return HasMany
     */
    public function renewals(): HasMany
    {
        return $this->hasMany(Order::class, 'parent_order_id');
    }

    /**
     * Define the original order
     *
     * @return BelongsTo
     */
    public function originalOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'parent_order_id');
    }

    /**
     * Define the category relation
     *
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Define the sub category relation
     *
     * @return BelongsTo
     */
    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class);
    }

    /**
     * Define the commissions relation
     *
     * @return MorphMany
     */
    public function commissions(): MorphMany
    {
        return $this->morphMany(Commission::class, 'payable');
    }

    /**
     * Define the rates relation (all ratings for this order)
     *
     * @return HasMany
     */
    public function rates(): HasMany
    {
        return $this->hasMany(Rate::class);
    }

    /**
     * Define the chat relation (single chat for this order)
     *
     * @return HasOne
     */
    public function chat(): HasOne
    {
        return $this->hasOne(Chat::class);
    }
}
