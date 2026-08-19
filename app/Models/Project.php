<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasUuid;
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = ['id'];


    protected $casts = [
        'min_price' => 'decimal:2',
        'max_price' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'cancelled_at' => 'datetime',
        'completed_at' => 'datetime',
        'started_at' => 'datetime'
    ];

    /**
     * Define the relation with the user
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define the attachments relation
     *
     * @return MorphMany
     */
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Define the proposals relation
     *
     * @return HasMany
     */
    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class);
    }

    /**
     * Define the selected proposal relation
     *
     * @return BelongsTo
     */
    public function selectedProposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class, 'selected_proposal_id');
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
     * Define the orders relation (polymorphic)
     *
     * @return MorphMany
     */
    public function orders(): MorphMany
    {
        return $this->morphMany(Order::class, 'orderable');
    }

    /**
     * Get the project rates (all ratings for this project)
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
        )->where('orders.orderable_type', self::class);
    }

    /**
     * Define the chat relation (polymorphic)
     *
     * @return MorphMany
     */
    public function chats(): MorphMany
    {
        return $this->morphMany(Chat::class, 'chattable');
    }
}
