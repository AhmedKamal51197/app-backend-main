<?php

namespace App\Enums;

use App\Traits\EnumToArray;

/**
 * A class defines the time period enums
 */
enum TimePeriodEnum: string
{
    use EnumToArray;

    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';
    case YEARLY = 'yearly';
}
