<?php

namespace App\Enums;

use App\Traits\EnumToArray;

/**
 * An enum class for contract type
 */
enum KycStatusEnum: string
{
    use EnumToArray;

    case PENDING = "pending";
    case APPROVED = "approved";
    case REJECTED = "rejected";
}
