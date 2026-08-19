<?php

namespace App\Enums;

use App\Traits\EnumToArray;

/**
 * An enum class for payment gateways
 */
enum PaymentGatewaysEnum: string
{
    use EnumToArray;

    case MYFATOORAH = "myfatoorah";
}
