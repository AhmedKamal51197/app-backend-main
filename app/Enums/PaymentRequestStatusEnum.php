<?php

namespace App\Enums;

use App\Traits\EnumToArray;

/**
 * An enum class for payment requests status enum
 */
enum PaymentRequestStatusEnum: string
{
    use EnumToArray;

    case PENDING = "pending";
    case APPROVED = "approved";
    case REJECTED = "rejected";
}
