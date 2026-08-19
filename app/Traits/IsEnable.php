<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * A trait class defines the query builder of is_enabled flag
 */
trait IsEnable
{
    /**
     * Filter results by is_enabled flag
     */
    public function scopeIsEnabled(Builder $query): Builder
    {
        return $query->where(['is_enabled' => true]);
    }
}
