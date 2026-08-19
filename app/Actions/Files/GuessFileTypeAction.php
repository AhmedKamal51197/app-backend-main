<?php

namespace App\Actions\Files;

use App\Enums\AttachmentFileTypeEnum;
use Exception;
use Illuminate\Http\UploadedFile;

/**
 * A class defines the guess file type action
 */
class GuessFileTypeAction
{
    /**
     * Guess what the type based on file extension
     *
     * @param UploadedFile|string $file
     * @return AttachmentFileTypeEnum
     * @throws Exception
     */
    public static function guess($file): AttachmentFileTypeEnum
    {
        // Get the file extension
        if ($file instanceof UploadedFile) {
            // For uploaded files, use getClientOriginalExtension()
            $extension = strtolower($file->getClientOriginalExtension());
        } elseif (is_string($file)) {
            // For file paths, use pathinfo
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        } else {
            throw new Exception('Invalid file type provided');
        }

        // Map extension to enum
        return match ($extension) {
            'png' => AttachmentFileTypeEnum::PNG,
            'gif' => AttachmentFileTypeEnum::GIF,
            'jpg', 'jpeg' => AttachmentFileTypeEnum::JPG,
            'svg' => AttachmentFileTypeEnum::SVG,
            'mp4' => AttachmentFileTypeEnum::MP4,
            'mov' => AttachmentFileTypeEnum::MOV,
            'mkv' => AttachmentFileTypeEnum::MKV,
            'webm' => AttachmentFileTypeEnum::WEBM,
            'wmv' => AttachmentFileTypeEnum::WMV,
            'mpeg', 'mpg' => AttachmentFileTypeEnum::MPEG,
            default => AttachmentFileTypeEnum::PDF,
        };
    }

}
