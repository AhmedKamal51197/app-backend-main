<?php

namespace App\Models;

use App\Enums\ReportStatusEnum;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A class defined for report
 */
class Report extends Model
{
    use HasFactory;
    use HasUuid;
    use SoftDeletes;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = ['id'];

    /**
     * Variables should be casts
     *
     * @var string[]
     */
    protected $casts = [
        'status' => ReportStatusEnum::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

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
     * Define the responses relation (chat messages)
     *
     * @return HasMany
     */
    public function responses(): HasMany
    {
        return $this->hasMany(Response::class);
    }

    /**
     * Define the latest response relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function latestResponse()
    {
        return $this->hasOne(Response::class)->latestOfMany();
    }

    /**
     * Get unread responses count for user
     *
     * @param User $user
     * @return int
     */
    public function getUnreadCountForUser(User $user): int
    {
        return $this->responses()
            ->where('sender_id', '!=', $user->id)
            ->where('created_at', '>', $this->updated_at)
            ->count();
    }
}
