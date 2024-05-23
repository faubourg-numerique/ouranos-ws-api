<?php

namespace API\Models;

use API\Traits\Updatable;
use Core\Model;

class WoTEvent extends Model
{
    use Updatable;

    const TYPE = "WoTEvent";

    public string $id;
    public string $name;
    public ?string $description = null;
    public string $capacityType;
    public string $capacityValue;
    public string $hasDataService;
    public string $hasWoTProperty;
    public string $hasWorkspace;
    public string $hasWoTThingDescription;

    public function toEntity(): Entity
    {
        $entity = new Entity();
        $entity->setId($this->id);
        $entity->setType(self::TYPE);
        $entity->setProperty("name", $this->name);
        if (!is_null($this->description)) {
            $entity->setProperty("description", $this->description);
        }
        $entity->setProperty("capacityType", $this->capacityType);
        $entity->setProperty("capacityValue", $this->capacityValue);
        $entity->setRelationship("hasDataService", $this->hasDataService);
        $entity->setRelationship("hasWoTProperty", $this->hasWoTProperty);
        $entity->setRelationship("hasWorkspace", $this->hasWorkspace);
        $entity->setRelationship("hasWoTThingDescription", $this->hasWoTThingDescription);
        return $entity;
    }

    public function fromEntity(Entity $entity): void
    {
        $this->id = $entity->getId();
        $this->name = $entity->getProperty("name");
        if ($entity->propertyExists("description")) {
            $this->description = $entity->getProperty("description");
        }
        $this->capacityType = $entity->getProperty("capacityType");
        $this->capacityValue = $entity->getProperty("capacityValue");
        $this->hasDataService = $entity->getRelationship("hasDataService");
        $this->hasWoTProperty = $entity->getRelationship("hasWoTProperty");
        $this->hasWorkspace = $entity->getRelationship("hasWorkspace");
        $this->hasWoTThingDescription = $entity->getRelationship("hasWoTThingDescription");
    }
}
