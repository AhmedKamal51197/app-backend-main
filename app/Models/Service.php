<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A class defines the Service model with relations
 */
class Service extends Model
{
    use HasUuid;
    use HasFactory;
    use SoftDeletes;

    /**
     * Guarded (id)
     *
     * @var string[]
     */
    protected $guarded = ['id'];

    /**
     * Adding sub category with
     *
     * @var string[]
     */
    protected $with = ['subCategory'];

    /**
     * Variables should be casts
     *
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'date',
        'updated_at' => 'date',
        'hidden' => 'boolean',
        'custom_offer' => 'boolean',
        'is_approved' => 'boolean',
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
     * Relation with attachments
     *
     * @return MorphMany
     */
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Define the relation with skills table
     *
     * @return HasMany
     */
    public function skills(): HasMany
    {
        return $this->hasMany(ServiceTag::class);
    }

    /**
     * Define the relation with service packages table
     *
     * @return HasMany
     */
    public function packages(): HasMany
    {
        return $this->hasMany(ServicePackage::class);
    }

    /**
     * Return the description thats with the user service
     *
     * @return string
     */
    public function description(): string
    {
        return $this->description_ar;
    }

    /**
     * Define the favorites relations
     *
     * @return HasMany
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(FavoriteService::class);
    }

    /**
     * Define if the service is favorite
     *
     * @return bool
     */
    public function isFavorite(): bool
    {
        $user = request()->user();

        return $this->favorites()
            ->where('user_id', $user->id)
            ->exists();
    }

    /**
     * Define the average rate
     *
     * @return float
     */
    public function averageRate(): float
    {
        return round($this->rates()->avg('rate') ?? 0, 2);
    }

    /**
     * Define the ratings count
     *
     * @return int
     */
    public function ratingsCount(): int
    {
        return $this->rates()->count();
    }

    /**
     * Get the rates through orders
     *
     * @return HasManyThrough
     */
    public function rates(): HasManyThrough
    {
        return $this->hasManyThrough(
            Rate::class,
            Order::class,
            'orderable_id',
            'order_id',
            'id',
            'id'
        )->where('orders.orderable_type', ServicePackage::class);
    }

    /**
     * Get all orders for this service through packages
     *
     * @return HasManyThrough
     */
    public function orders(): HasManyThrough
    {
        return $this->hasManyThrough(
            Order::class,
            ServicePackage::class,
            'service_id',
            'orderable_id',
            'id',
            'id'
        )->where('orders.orderable_type', ServicePackage::class);
    }

    /**
     * Define the chats relation (polymorphic)
     *
     * @return MorphMany
     */
    public function chats(): MorphMany
    {
        return $this->morphMany(Chat::class, 'chattable');
    }
}
