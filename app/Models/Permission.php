<?php

namespace App\Models;

use App\Traits\HasUuid;
use Spatie\Permission\Models\Permission as SpatiePermission;

/**
 * A model to define permissions with UUID support
 */
class Permission extends SpatiePermission
{
    use HasUuid;
}
