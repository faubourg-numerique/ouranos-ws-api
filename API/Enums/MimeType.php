<?php

namespace API\Enums;

use ArchTech\Enums\Values;

enum MimeType: string
{
    use Values;

    case Json = "application/json";
    case XWWWFormUrlEncoded = "application/x-www-form-urlencoded";
    case TextPlain = "text/plain";
}
