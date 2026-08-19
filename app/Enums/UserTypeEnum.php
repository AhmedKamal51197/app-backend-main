<?php

namespace App\Enums;

use App\Traits\EnumToArray;

/**
 * A class defines the user type enums
 */
enum UserTypeEnum: string
{
    use EnumToArray;

    case MERCHANT = 'merchant';
    case FARMER = 'farmer';
}
