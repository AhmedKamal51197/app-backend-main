<?php

namespace App\Enums;

use App\Traits\EnumToArray;

/**
 * An enum class for service type enum
 */
enum ServiceTypeEnum: string
{
    use EnumToArray;

    case ONE_TIME = "one_time";
    case PART_TIME = "part_time";
}
