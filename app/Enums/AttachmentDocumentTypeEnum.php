<?php

namespace App\Enums;

use App\Traits\EnumToArray;

/**
 * An enum class for attachment document type enum
 */
enum AttachmentDocumentTypeEnum: string
{
    use EnumToArray;

    case IDENTITY_CARD_FRONT = "identity_card_front";
    case IDENTITY_CARD_BACK = "identity_card_back";
    case USER_HOLDING_ID = "user_holding_id";
    case IDENTITY_CARD = "identity_card";
    case PASSPORT = "passport";
    case VIDEO = "video";
    case PICTURE = "picture";
    case DOCUMENT = "document";
    case PORTFOLIO = "portfolio";
    case PHOTO = "photo";
    case LOGO = "logo";
    case CERTIFICATE = "certificate";
    case SIGNATURE = "signature";
    case CONTRACT = "contract";
}
