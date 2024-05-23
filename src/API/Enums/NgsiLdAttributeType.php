<?php

namespace API\Enums;

use ArchTech\Enums\Values;

enum NgsiLdAttributeType: string
{
    use Values;

    case Property = "Property";
    case GeoProperty = "GeoProperty";
    case Relationship = "Relationship";
}
