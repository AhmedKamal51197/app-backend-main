<?php

namespace App\Enums;

use App\Traits\EnumToArray;

/**
 * A class defines the report status enums
 */
enum ReportStatusEnum: string
{
    use EnumToArray;

    case OPENED = 'opened';
    case CLOSED = 'closed';
}
