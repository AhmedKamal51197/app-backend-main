<?php
namespace App\Enums;
use App\Traits\EnumToArray;

/**
 * An enum class to define the user gender
 */
enum UserGenderEnum: string
{
    use EnumToArray;
    case MALE = "male";
    case FEMALE = "female";
    case OTHER = "other";
}
