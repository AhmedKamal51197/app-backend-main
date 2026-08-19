<?php

namespace App\Enums;

use App\Traits\EnumToArray;

/**
 * An enum class defines the user types
 */
enum UserTypesEnum: string
{
    use EnumToArray;

    case SEEKER = 'seeker';
    case MERCHANT = 'merchant';
    case PROVIDER = 'provider';
}
