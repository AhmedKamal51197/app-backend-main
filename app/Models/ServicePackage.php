<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A class defines the service package model with relations
 */
class ServicePackage extends Model
{
    use HasUuid;
    use SoftDeletes;

    /**
     * Guarded (id)
     *
     * @var string[]
     */
    protected $guarded = ['id'];

    protected $with = ['features'];

    /**
     * Return the title thats with the user service package
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

    /**
     * Define the service relation
     *
     * @return BelongsTo
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Define the features relation
     *
     * @return HasMany
     */
    public function features(): HasMany
    {
        return $this->hasMany(PackageFeature::class);
    }

    /**
     * Define the orders relation
     *
     * @return MorphMany
     */
    public function orders(): MorphMany
    {
        return $this->morphMany(Order::class, 'orderable');
    }
}
