<?php

namespace App\Actions\Notification;

use App\Enums\NotificationReferenceEnum;
use App\Models\Notification;
use App\Models\User;
use Exception;

/**
 * A class defines the notification actions
 */
class NotificationAction
{
    /**
     * Send Notification
     *
     * @param User $user
     * @param NotificationReferenceEnum $reference
     * @param string $referenceId
     * @param bool $important
     * @param string $title
     * @param string $body
     *
     * @return void
     */
    public static function send(User $user, NotificationReferenceEnum $reference, string $referenceId, string $title, string $body, bool $important): void
    {
        try {
            Notification::create([
                'user_id' => $user->getAttribute('id'),
                'important' => $important,
                'body' => $body,
                'title' => $title,
                'reference' => $reference->value,
                'reference_id' => $referenceId,
            ]);
        } catch (Exception $exception) {
        }
    }
}
