<?php

namespace API\Models;

class TemporalEntityProperty
{
    public string $id;
    public string $name;
    public ?int $fromTime = null;
    public ?int $toTime = null;
    public array $data;
}
