<?php

namespace App\Services\Payment\Tap;

use RuntimeException;

/**
 * Tap API or business-rule failure while paying or refunding with Tap
 */
class TapException extends RuntimeException
{
}
