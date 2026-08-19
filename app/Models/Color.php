<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Color extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasUuid;

    protected $guarded = ['id', 'uuid'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Return the name that's with the user locale
     */
    public function name(): string
    {
        $lang = app()->getLocale();

        if (isset($this->attributes['name_' . $lang])) {
            return $this->attributes['name_' . $lang];
        }
        return $this->attributes['name_en'] ?? '';
    }
}