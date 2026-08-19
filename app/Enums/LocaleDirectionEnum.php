<?php

namespace App\Enums;

use App\Traits\EnumToArray;

/**
 * An enum class for locales
 */
enum LocaleDirectionEnum: string
{
    use EnumToArray;

    case LTR = "ltr";
    case RTL = "rtl";
}
