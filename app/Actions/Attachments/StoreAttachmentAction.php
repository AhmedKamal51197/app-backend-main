<?php

namespace App\Actions\Attachments;

use App\Actions\Files\GuessFileTypeAction;
use App\Services\Setting\SettingService;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * A class defines the action to store attachment
 */
class StoreAttachmentAction
{
    /**
     * Store attachment files
     *
     * @param Model $model
     * @param mixed $files
     * @param string $relationName
     *
     * @return void
     *
     * @throws Exception
     */
    public static function store(Model $model, mixed $files, string $relationName, bool $deleteOld = true): void
    {
        if ($deleteOld && $model->$relationName()->exists()) {

            foreach ($model->$relationName()->get() as $attachment) {
                Storage::disk($attachment->disk)->delete($attachment->path);
            }

            $model->$relationName()->delete();
        }

        $files = is_array($files) ? $files : [$files];

        foreach ($files as $item) {

            $file = $item;
            $documentType = null;

            if (is_array($item)) {
                $file = $item['file'];
                $documentType = $item['file_type'];
            }

            $disk = SettingService::getAttachmentStorage();

            $filePath = Storage::disk($disk)->put(strtolower(class_basename($model)) . 's' . '/' . $model->id, $file);

            $attachmentData = [
                'disk' => $disk,
                'path' => $filePath,
                'file_meta' => json_encode([
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                ]),
                'type' => GuessFileTypeAction::guess($file)->value,
                'user_id' => request()->user()->id,
            ];

            if ($documentType) {
                $attachmentData['document_type'] = $documentType;
            }

            $model->{$relationName}()->create($attachmentData);
        }
    }
}
