<?php

namespace App\Models;

use App\Enums\AttachmentDocumentTypeEnum;
use App\Traits\HasUuid;
use App\Traits\IsEnable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * A class defines the certificate provider model with relations
 */
class CertificateProvider extends Model
{
    use IsEnable;
    use HasUuid;
    use SoftDeletes;
    use HasFactory;

    protected $guarded = ['id'];


    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Define the certificates for providers
     *
     * @return HasMany
     */
    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class, 'certificate_provider_id');
    }

    /**
     * Adding the boot
     *
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($provider) {
            if (empty($provider->slug)) {
                $provider->slug = Str::slug($provider->name);
            }
        });
    }

    /**
     * Define the certificate provider attachments
     *
     * @return MorphMany
     */
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Define the logo
     *
     * @return MorphOne
     */
    public function logo():MorphOne
    {
        return $this->morphOne(Attachment::class, 'attachable')
            ->where('document_type', AttachmentDocumentTypeEnum::LOGO->value);
    }

    /**
     * Return the title thats with the user locale
     *
     * @return string
     */
    public function title():string
    {
        return $this->title_ar;
    }
}
