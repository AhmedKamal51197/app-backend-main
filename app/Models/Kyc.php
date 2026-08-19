<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A class defines the KYC model with relations
 */
class Kyc extends Model
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
     * Variables should be casts
     *
     * @var string[]
     */
    protected $casts = [
        'birth_date' => 'date',
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
     * Define the relation with Country model
     *
     * @return BelongsTo
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
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
}
