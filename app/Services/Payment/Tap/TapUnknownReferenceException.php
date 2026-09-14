<?php

namespace App\Services\Payment\Tap;

/**
 * A Tap charge or refund that was not created by this application
 */
class TapUnknownReferenceException extends TapException
{
}
