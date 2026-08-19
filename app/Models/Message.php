<?php

namespace App\Models;

use App\Enums\MessageTypeEnum;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A class defines the Message model
 */
class Message extends Model
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
        'type' => MessageTypeEnum::class,
        'is_edited' => 'boolean',
        'edited_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Define the chat relation
     *
     * @return BelongsTo
     */
    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

    /**
     * Define the sender relation
     *
     * @return BelongsTo
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Define the reply relation
     *
     * @return BelongsTo
     */
    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'reply_to_id');
    }

    /**
     * Define the replies relation
     *
     * @return HasMany
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Message::class, 'reply_to_id');
    }

    /**
     * Define the messageable relation (morph)
     *
     * @return MorphTo
     */
    public function messageable(): MorphTo
    {
        return $this->morphTo();
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
}
