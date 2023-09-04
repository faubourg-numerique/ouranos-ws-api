<?php

namespace API\Enums;

use ArchTech\Enums\Values;

enum NgsiLdGeoPropertyType: string
{
    use Values;

    case Point = "Point";
    case MultiPoint = "MultiPoint";
    case LineString = "LineString";
    case MultiLineString = "MultiLineString";
    case Polygon = "Polygon";
    case MultiPolygon = "MultiPolygon";
}
