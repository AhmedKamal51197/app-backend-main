<?php

namespace App\Enums;

use App\Traits\EnumToArray;

/**
 * Role type enum
 */
enum RoleTypeEnum: string
{
    use EnumToArray;

    case ADMINISTRATIVE = 'administrative';
    case FINANCIAL = 'financial';
    case SUPPORT = 'support';
    case MARKETING = 'marketing';
    case TECHNICAL = 'technical';
    case REVIEW = 'review';
    case VIEW_ONLY = 'view_only';
    case CUSTOM = 'custom';
}
