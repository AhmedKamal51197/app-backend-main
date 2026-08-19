<?php

namespace App\Enums;

use App\Traits\EnumToArray;

/**
 * A class defines the order status enums
 */
enum OrderStatusEnum: string
{
    use EnumToArray;

    case APPROVAL_PENDING = 'approval_pending';
    case IN_PROGRESS = 'in_progress';
    case REJECTED = 'rejected';
    case COMPLETED = 'completed';
    case RELEASE_PENDING = 'release_pending';
    case RELEASED = 'released';
    case DISPUTED = 'disputed';
    case REFUNDED = 'refunded';
    case CANCEL_PENDING = 'cancel_pending';
    case CANCELLED = 'cancelled';
    case REVISION = 'revision';
}
