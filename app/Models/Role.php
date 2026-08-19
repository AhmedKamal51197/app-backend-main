<?php

namespace App\Models;

use App\Traits\HasUuid;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * A model to assign role to users, every user should have a role, the role have permissions
 */
class Role extends SpatieRole
{
    use HasUuid;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = ['is_active' => 'boolean'];

    /**
     * The role have many permissions
     *
     * @return BelongsToMany
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions');
    }
}
