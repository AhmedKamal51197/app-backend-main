<?php

namespace App\Enums;

use App\Traits\EnumToArray;

/**
 * An enum class for employment type
 */
enum EmploymentTypeEnum: string
{
    use EnumToArray;

    case PART_TIME = "part_time";
    case FULL_TIME = "full_time";
    case FREELANCER = "freelancer";
    case INTERNSHIP = "internship";
}
