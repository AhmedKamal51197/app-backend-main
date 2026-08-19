<?php

namespace App\Enums;

use App\Traits\EnumToArray;

/**
 * An enum class for chat type
 */
enum ChatTypeEnum: string
{
    use EnumToArray;

    case USER = 'user';
    case TEAM = 'team';
    case SERVICE = 'service';
    case JOB = 'job';
    case PROJECT = 'project';
}
