<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum BannerTypeEnum: string
{
    use EnumToArray;

    case MAIN = 'main';
    case CATEGORY = 'category';
}
