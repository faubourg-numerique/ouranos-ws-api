<?php

namespace API\Enums;

use ArchTech\Enums\Values;

enum IdentityManagerGrantType: string
{
    use Values;

    case ClientCredentials = "client_credentials";
    case Password = "password";
}
