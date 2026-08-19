<?php

namespace App\Services\Response;

use App\Actions\Attachments\StoreAttachmentAction;
use App\Enums\MessageTypeEnum;
use App\Events\LogExceptionEvent;
use App\Models\Report;
use App\Models\Response;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * Class Response service to handle report responses (chat-like)
 */
class ResponseService
{
    /**
     * Store response (send message in report chat)
     *
     * @param Report $report
     * @param User $sender
     * @param array $data
     *
     * @return Response
     *
     * @throws Exception
     */
    public function store(Report $report, User $sender, array $data): Response
    {
        DB::beginTransaction();
        try {

            $response = Response::create([
                'report_id' => $report->getAttribute('id'),
                'sender_id' => $sender->getAttribute('id'),
                'content' => $data['content'] ?? null,
                'type' => $data['type'] ?? MessageTypeEnum::TEXT->value,
            ]);

            if (isset($data['attachments'])) {
                StoreAttachmentAction::store(
                    $response,
                    $data['attachments'],
                    'attachments',
                    false
                );
            }

            // Update report timestamp
            $report->touch();

            DB::commit();

            return $response->fresh(['sender', 'attachments']);

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

}
