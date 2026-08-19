<?php

namespace App\Models;

use App\Enums\ChatTypeEnum;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A class defines the Chat model
 */
class Chat extends Model
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
     * Variables should be casts
     *
     * @var string[]
     */
    protected $casts = [
        'type' => ChatTypeEnum::class,
        'last_message_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Define the creator relation
     *
     * @return BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Define the participants relation
     *
     * @return BelongsToMany
     */
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chat_participants')
            ->withPivot(['joined_at', 'last_read_at', 'is_admin'])
            ->withTimestamps();
    }

    /**
     * Define the messages relation
     *
     * @return HasMany
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Define the latest message relation
     *
     * @return HasMany
     */
    public function latestMessage(): HasMany
    {
        return $this->messages()->latest();
    }

    /**
     * Define the chattable relation (morph)
     *
     * @return MorphTo
     */
    public function chattable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Define the order relation
     *
     * @return BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get unread messages count for a user
     *
     * @param User $user
     * @return int
     */
    public function getUnreadCountForUser(User $user): int
    {
        $participant = $this->participants()->where('user_id', $user->id)->first();

        if (!$participant) {
            return 0;
        }

        return $this->messages()
            ->where('sender_id', '!=', $user->id)
            ->where('created_at', '>', $participant->pivot->last_read_at ?? $participant->pivot->joined_at)
            ->count();
    }
}
