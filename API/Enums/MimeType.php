<?php

namespace API\Enums;

use ArchTech\Enums\Values;

enum MimeType: string
{
    use Values;

    case Json = "application/json";
}
