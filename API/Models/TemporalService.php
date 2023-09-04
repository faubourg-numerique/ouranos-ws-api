<?php

namespace API\Models;

use API\Traits\Updatable;
use Core\Model;

abstract class TemporalService extends Model
{
    use Updatable;

    const TYPE = "TemporalService";

    public string $id;
    public string $name;
    public ?string $description = null;
    public string $temporalServiceType;
}
