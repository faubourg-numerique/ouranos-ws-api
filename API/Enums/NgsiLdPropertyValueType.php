<?php

namespace API\Enums;

use ArchTech\Enums\Values;

enum NgsiLdPropertyValueType: string
{
    use Values;

    case String = "String";
    case Number = "Number";
    case Boolean = "Boolean";
    case Object = "Object";
    case Array = "Array";
}
