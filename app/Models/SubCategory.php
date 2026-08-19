<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\IsEnable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A class defines the sub category model with relations
 */
class SubCategory extends Model
{
    use IsEnable;
    use HasUuid;
    use SoftDeletes;
    use HasFactory;

    protected $guarded = ['id'];

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

    /**
     * Define the relation with the users
     *
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
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
