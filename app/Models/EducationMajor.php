<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A class defines the education major model with relations
 */
class EducationMajor extends Model
{
    use HasUuid;
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Return the title thats with the user education major
     *
     * @return string
     */
    public function title(): string
    {
        $lang =  app()->getLocale();

        if (isset($this->attributes['title_' . $lang])) {
            return $this->attributes['title_' . $lang] ;
        }
        return "";
    }
}
