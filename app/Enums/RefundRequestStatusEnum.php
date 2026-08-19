<?php

namespace App\Enums;

use App\Traits\EnumToArray;

/**
 * An enum class for refund requests status enum
 */
enum RefundRequestStatusEnum: string
{
    use EnumToArray;

    case PENDING = "pending";
    case APPROVED = "approved";
    case REJECTED = "rejected";
}
