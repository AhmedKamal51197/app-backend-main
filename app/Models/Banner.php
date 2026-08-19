<?php

namespace App\Models;

use App\Enums\BannerTypeEnum;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Banner extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasUuid;

    protected $guarded = ['id', 'uuid'];

    protected $casts = ['is_active' => 'boolean'];

    /**
     * Return the title that's with the user locale
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
     * Return the description that's with the user locale
     */
    public function description(): string
    {
        $lang = app()->getLocale();

        if (isset($this->attributes['description_' . $lang])) {
            return $this->attributes['description_' . $lang];
        }
        return "";
    }

    /**
     * Get the banner's image attachment
     */
    public function attachment(): MorphOne
    {
        return $this->morphOne(Attachment::class, 'attachable');
    }
}

