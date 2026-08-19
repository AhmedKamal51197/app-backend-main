<?php

namespace App\Enums;

use App\Traits\EnumToArray;

/**
 * An enum class for attachment storage enum
 */
enum AttachmentStorageEnum: string
{
    use EnumToArray;

    case S3 = "s3";
    case LOCAL = "local";
    case BLOB = "azure";
}
