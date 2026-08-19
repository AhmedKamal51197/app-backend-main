<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Notifications\Order\NewOrderNotification;
use App\Notifications\Order\OrderCompletedNotification;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

echo "=================================================\n";
echo "FCM Push Notification Test Script\n";
echo "=================================================\n\n";

// 1. Find a user with an FCM token
$user = User::whereNotNull('fcm_token')->first();

if (!$user) {
    echo "No user with an fcm_token found in the database.\n";
    echo "To test this properly, you must log into the mobile app to set the fcm_token for a user.\n";
    echo "Alternatively, you can manually update a user's fcm_token in the database to a valid token.\n";
    exit;
}

echo "Found User: {$user->name} (ID: {$user->id})\n";
echo "FCM Token: " . substr($user->fcm_token, 0, 15) . "... \n\n";

// 2. Test sending a raw FCM message directly using Kreait
echo "Testing raw FCM push... ";
try {
    $messaging = app('firebase.messaging');
    $message = CloudMessage::withTarget('token', $user->fcm_token)
        ->withNotification(Notification::create('Raw Test', 'This is a raw Firebase test message'));
    $messaging->send($message);
    echo "SUCCESS!\n";
} catch (\Exception $e) {
    echo "FAILED: " . $e->getMessage() . "\n";
}

// 3. Test sending using Laravel Notification System (FcmChannel)
echo "Testing FcmChannel (NewOrderNotification)... ";
try {
    $user->notify(new NewOrderNotification('ORD-12345', 'Sample Project Design'));
    echo "SUCCESS!\n";
} catch (\Exception $e) {
    echo "FAILED: " . $e->getMessage() . "\n";
}

echo "Testing FcmChannel (OrderCompletedNotification)... ";
try {
    $user->notify(new OrderCompletedNotification('ORD-12345', 'Sample Project Design'));
    echo "SUCCESS!\n";
} catch (\Exception $e) {
    echo "FAILED: " . $e->getMessage() . "\n";
}

echo "\nDone!\n";
