<?php

namespace API\Enums;

use ArchTech\Enums\Values;

enum IdentityManagerImplementationName: string
{
    use Values;

    case Keyrock = "keyrock";
}
