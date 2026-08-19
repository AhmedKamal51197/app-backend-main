<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\IsEnable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A class defines the category model with relations
 */
class Category extends Model
{
    use IsEnable;
    use HasUuid;
    use SoftDeletes;
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Define the relation with sub categories
     *
     * @return HasMany
     */
    public function subCategories(): HasMany
    {
        return $this->hasMany(SubCategory::class);
    }

    /**
     * Return the title thats with the user locale
     *
     * @return string
     */
    public function title():string
    {
        $lang =  app()->getLocale();

        if (isset($this->attributes['title_' . $lang])) {
            return $this->attributes['title_' . $lang] ;
        }
        return "";
    }

    /**
     * Define the users relation
     *
     * @return HasMany
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Define the relation with skills
     *
     * @return HasMany
     */
    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class);
    }

    /**
     * Define the relation with features
     *
     * @return HasMany
     */
    public function features(): HasMany
    {
        return $this->hasMany(Feature::class);
    }

    /**
     * Define the relation with image
     *
     * @return MorphOne
     */
    public function image(): MorphOne
    {
        return $this->morphOne(Attachment::class, 'attachable');
    }
}
