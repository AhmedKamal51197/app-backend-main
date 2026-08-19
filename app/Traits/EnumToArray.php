<?php

namespace App\Traits;

/**
 * A trait class to convert Enum to Array
 */
trait EnumToArray
{
    /**
     * A static function convert enum to array
     */
    public static function toArray(): array
    {
        return array_map(
            fn(self $enum) => $enum->value,
            self::cases()
        );
    }
}
