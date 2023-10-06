<?php

namespace API\Enums;

use ArchTech\Enums\Values;

enum AuthorizationMode: string
{
    use Values;

    case OAuth2 = "oauth2";
    case SIOP2 = "siop2";
}
