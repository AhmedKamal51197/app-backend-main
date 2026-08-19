<?php

namespace App\Enums;

use App\Traits\EnumToArray;

/**
 * A class defines the project status enums
 */
enum ProjectStatusEnum: string
{
    use EnumToArray;

    case DRAFT = 'draft';
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case DISPUTED = 'disputed';
    case REFUNDED = 'refunded';
    case CANCELLED = 'cancelled';
    case CANCEL_PENDING = 'cancel_pending';
}
