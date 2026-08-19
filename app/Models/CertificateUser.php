<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\IsEnable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A class defines the certificate user model with relations
 */
class CertificateUser extends Model
{
    use IsEnable;
    use HasUuid;
    use SoftDeletes;
    use HasFactory;

    protected $table = 'certificate_user';

    protected $with = 'file';

    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'completion_date' => 'date',
        'expiry_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Define the certificate relation
     *
     * @return BelongsTo
     */
    public function certificate(): BelongsTo
    {
        return $this->belongsTo(Certificate::class);
    }

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
     * Check if certificate is expired
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        if (!$this->expiry_date) {
            return false;
        }

        return $this->expiry_date->isPast();
    }

    /**
     * Define the file relation
     *
     * @return MorphOne
     */
    public function file(): MorphOne
    {
        return $this->morphOne(Attachment::class, 'attachable');
    }
}
