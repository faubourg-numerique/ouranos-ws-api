<?php

namespace API\Models;

use API\Traits\Updatable;
use Core\Model;

class WoTThingDescription extends Model
{
    use Updatable;

    const TYPE = "WoTThingDescription";

    public string $name;
    public ?string $description = null;
    public array $positionInChart;
    public string $hasWorkspace;

    public function toEntity(): Entity
    {
        $entity = new Entity();
        $entity->setType(self::TYPE);
        $entity->setProperty("name", $this->name);
        if (!is_null($this->description)) {
            $entity->setProperty("description", $this->description);
        }
        $entity->setProperty("positionInChart", $this->positionInChart);
        $entity->setRelationship("hasWorkspace", $this->hasWorkspace);
        return $entity;
    }

    public function fromEntity(Entity $entity): void
    {
        $this->name = $entity->getProperty("name");
        if ($entity->propertyExists("description")) {
            $this->description = $entity->getProperty("description");
        }
        $this->positionInChart = $entity->getProperty("positionInChart");
        $this->hasWorkspace = $entity->getRelationship("hasWorkspace");
    }
}
