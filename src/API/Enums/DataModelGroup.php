<?php

namespace API\Enums;

use ArchTech\Enums\Values;

enum DataModelGroup: string
{
    use Values;

    case Equipment = "equipment";
    case EquipmentModel = "equipment-model";
    case Infrastructure = "infrastructure";
    case Control = "control";
    case ObservationDataCollection = "observation-data-collection";
}
