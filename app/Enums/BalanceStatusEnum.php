<?php

namespace App\Enums;

use App\Traits\EnumToArray;

/**
 * An enum class for balance status enum
 */
enum BalanceStatusEnum: string
{
    use EnumToArray;

    case PENDING = "pending";
    case COMPLETED = "completed";
    case REJECTED = "rejected";
}
