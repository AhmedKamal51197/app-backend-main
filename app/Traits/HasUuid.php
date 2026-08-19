<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

/**
 * A trait class defines the UUIDs
 */
trait HasUuid
{
    use HasUuids;

    /**
     * Get the columns that should receive a unique identifier.
     */
    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
