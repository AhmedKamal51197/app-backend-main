<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\IsEnable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A class defines the country model with relations
 */
class Country extends Model
{
    use IsEnable;
    use HasUuid;
    use SoftDeletes;
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = ['is_enabled' => 'boolean'];

    /**
     * Return the name that with the country
     *
     * @return string
     */
    public function name(): string
    {
        $lang = app()->getLocale();

        if ($lang == 'ar') {
            return $this->arabic_name;
        } else {
            return $this->english_name;
        }
    }
}
