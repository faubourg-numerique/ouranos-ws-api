<?php

namespace API\Enums;

use ArchTech\Enums\Values;

enum VCVerifierImplementationName: string
{
    use Values;

    case FiwareVCVerifier = "fiware-vc-verifier";
}
