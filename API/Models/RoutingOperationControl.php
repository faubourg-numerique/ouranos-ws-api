<?php

namespace API\Models;

use API\Traits\Updatable;
use Core\Model;

class RoutingOperationControl extends Model
{
    use Updatable;

    const TYPE = "RoutingOperationControl";

    public string $controlledPropertyValue;
    public string $hasRoutingOperation;
    public string $hasControlledProperty;
    public string $hasWorkspace;

    public function toEntity(): Entity
    {
        $entity = new Entity();
        $entity->setType(self::TYPE);
        $entity->setProperty("controlledPropertyValue", $this->controlledPropertyValue);
        $entity->setRelationship("hasRoutingOperation", $this->hasRoutingOperation);
        $entity->setRelationship("hasControlledProperty", $this->hasControlledProperty);
        $entity->setRelationship("hasWorkspace", $this->hasWorkspace);
        return $entity;
    }

    public function fromEntity(Entity $entity): void
    {
        $this->controlledPropertyValue = $entity->getProperty("controlledPropertyValue");
        $this->hasRoutingOperation = $entity->getRelationship("hasRoutingOperation");
        $this->hasControlledProperty = $entity->getRelationship("hasControlledProperty");
        $this->hasWorkspace = $entity->getRelationship("hasWorkspace");
    }
}
