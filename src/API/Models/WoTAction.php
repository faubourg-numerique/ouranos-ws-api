<?php

namespace API\Models;

use API\Traits\Updatable;
use Core\Model;

class WoTAction extends Model
{
    use Updatable;

    const TYPE = "WoTAction";

    public string $id;
    public string $name;
    public ?string $description = null;
    public string $hasWoTThingDescription;
    public string $hasDataService;
    public string $hasWorkspace;

    public function toEntity(): Entity
    {
        $entity = new Entity();
        $entity->setId($this->id);
        $entity->setType(self::TYPE);
        $entity->setProperty("name", $this->name);
        if (!is_null($this->description)) {
            $entity->setProperty("description", $this->description);
        }
        $entity->setRelationship("hasWoTThingDescription", $this->hasWoTThingDescription);
        $entity->setRelationship("hasDataService", $this->hasDataService);
        $entity->setRelationship("hasWorkspace", $this->hasWorkspace);
        return $entity;
    }

    public function fromEntity(Entity $entity): void
    {
        $this->id = $entity->getId();
        $this->name = $entity->getProperty("name");
        if ($entity->propertyExists("description")) {
            $this->description = $entity->getProperty("description");
        }
        $this->hasWoTThingDescription = $entity->getRelationship("hasWoTThingDescription");
        $this->hasDataService = $entity->getRelationship("hasDataService");
        $this->hasWorkspace = $entity->getRelationship("hasWorkspace");
    }
}
