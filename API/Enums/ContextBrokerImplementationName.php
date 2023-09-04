<?php

namespace API\Enums;

use ArchTech\Enums\Values;

enum ContextBrokerImplementationName: string
{
    use Values;

    case OrionLd = "orion-ld";
    case Scorpio = "scorpio";
    case Stellio = "stellio";
}
