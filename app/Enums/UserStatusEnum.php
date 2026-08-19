<?php
namespace App\Enums;
use App\Traits\EnumToArray;

/**
 * An enum class to define the user status
 */
enum UserStatusEnum: string
{
    use EnumToArray;
    case ACTIVE = "active";
    case BUSY = "busy";
    case DEACTIVATED = "deactivated";
    case IN_VACATION = "in_vacation";
}
