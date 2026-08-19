<?php

namespace App\Enums;

use App\Traits\EnumToArray;

/**
 * Role access level enum
 */
enum RoleAccessLevelEnum: string
{
    use EnumToArray;

    case FULL_ACCESS = 'full_access';
    case EDIT_ONLY = 'edit_only';
    case CUSTOM = 'custom';
}
