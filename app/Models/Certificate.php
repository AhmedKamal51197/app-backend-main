<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\IsEnable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * A class defined for the certificate
 */
class Certificate extends Model
{
    use HasFactory;
    use HasUuid;
    use IsEnable;
    use SoftDeletes;

    protected $guarded = ['id'];


    protected $casts = [
        'completion_date' => 'date',
        'expiry_date' => 'date',
        'skills' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Define the relation with provider
     *
     * @return BelongsTo
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(CertificateProvider::class, 'provider_id');
    }

    /**
     * Adding the boot function
     *
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($certificate) {
            if (empty($certificate->slug)) {
                $certificate->slug = Str::slug($certificate->name);
            }
        });
    }

    /**
     * Define the relation with certificate users table
     *
     * @return HasMany
     */
    public function userCertificates(): HasMany
    {
        return $this->hasMany(CertificateUser::class);
    }

    /**
     * Return the title thats with the user locale
     *
     * @return string
     */
    public function title(): string
    {
        $lang = app()->getLocale();

        if (isset($this->attributes['title_' . $lang])) {
            return $this->attributes['title_' . $lang];
        }
        return "";
    }
}

