<?php

namespace App\Enums;

use App\Traits\EnumToArray;

/**
 * A class defines the order direction enums
 */
enum OrderDirectionEnum: string
{
    use EnumToArray;

    case LATEST = 'latest';
    case OLDEST = 'oldest';
}
