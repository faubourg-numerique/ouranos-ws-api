<?php

namespace API\Enums;

use ArchTech\Enums\Values;

enum TemporalService: string
{
    use Values;

    case NgsiLd = "ngsi-ld";
    case Mintaka = "mintaka";
}
