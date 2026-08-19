<?php

namespace App\Listeners;

use App\Events\LogExceptionEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LogExceptionListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param LogExceptionEvent $event
     */
    public function handle(LogExceptionEvent $event): void
    {
        $exception = $event->exception;
        $payload = $event->payload;
        $message = $event->message;

        Log::error($message ?? $exception->getMessage(), [
            'user' => Auth::check() ? request()->user() : 'system',
            'payload' => $payload,
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'previous' => $exception->getPrevious(),
            'code' => $exception->getCode(),
        ]);
    }
}
