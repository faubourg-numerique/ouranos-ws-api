<?php

namespace API\Enums;

use ArchTech\Enums\Values;

enum Scheme: string
{
    use Values;

    case Http = "http";
    case Https = "https";
}
