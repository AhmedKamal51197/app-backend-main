<?php

namespace App\Services\Order;

use App\Actions\Attachments\StoreAttachmentAction;
use App\Actions\Files\GuessFileTypeAction;
use App\Enums\AttachmentDocumentTypeEnum;
use App\Enums\AttachmentStorageEnum;
use App\Events\LogExceptionEvent;
use App\Models\Order;
use App\Models\OrderMessage;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * A class defines the order message service
 */
class OrderMessageService
{
    /**
     * Store new order message
     *
     * @param User $user
     * @param Order $order
     * @param array $data
     *
     * @return OrderMessage
     *
     * @throws Exception
     */
    public function store(User $user, Order $order, array $data): OrderMessage
    {
        DB::beginTransaction();
        try {

            $message = OrderMessage::create([
                'user_id' => $user->getAttribute('id'),
                'order_id' => $order->getAttribute('id'),
                'message' => $data['text'] ?? "",
            ]);

            if (isset($data['file'])) {
                StoreAttachmentAction::store($message, $data['file'], 'file', false);
            }

            DB::commit();

            return $message->load('file');

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }
}
