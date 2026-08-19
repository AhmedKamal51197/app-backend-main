<?php

namespace App\Enums;

use App\Traits\EnumToArray;

/**
 * An enum class for certificate level
 */
enum CertificateLevelEnum: string
{
    use EnumToArray;

    case BEGINNER = "beginner";
    case INTERMEDIATE = "intermediate";
    case ADVANCED = "advanced";
    case PROFESSIONAL = "professional";
}
