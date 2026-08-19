<?php

namespace App\Enums;

use App\Traits\EnumToArray;

/**
 * An enum class for attachment file type enum
 */
enum AttachmentFileTypeEnum: string
{
    use EnumToArray;

    case PNG = "png";
    case GIF = "gif";
    case JPG = "jpg";
    case SVG = "svg";
    case PDF = "pdf";
    case MP4 = "mp4";
    case MOV = "mov";
    case MKV = "mkv";
    case WEBM = "webm";
    case WMV = "wmv";
    case MPEG = "mpeg";
}
