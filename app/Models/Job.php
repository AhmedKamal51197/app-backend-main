<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A class defines the Job model with relations
 */
class Job extends Model
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
        'created_at' => 'date',
        'updated_at' => 'date',
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
        return $this->hasMany(JobTag::class);
    }

    /**
     * Return the description thats with the user service
     *
     * @return string
     */
    public function description():string
    {
        return $this->description_ar;
    }

    /**
     * Adding the morph relation
     *
     * @return MorphMany
     */
    public function chats(): MorphMany
    {
        return $this->morphMany(Chat::class, 'chattable');
    }
}


