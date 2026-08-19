<?php

namespace App\Enums;

use App\Traits\EnumToArray;

/**
 * An enum class for notification reference type
 */
enum NotificationReferenceEnum: string
{
    use EnumToArray;

    case SERVICE = 'service';
    case SYSTEM = 'system';
    case CHAT = 'chat';
    case ORDER = 'order';
}
