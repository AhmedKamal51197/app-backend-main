<?php

namespace App\Enums;

use App\Traits\EnumToArray;

/**
 * An enum class for message type
 */
enum MessageTypeEnum: string
{
    use EnumToArray;

    case TEXT = 'text';
    case ATTACHMENT = 'attachment';
    case OFFER = 'offer';
}
