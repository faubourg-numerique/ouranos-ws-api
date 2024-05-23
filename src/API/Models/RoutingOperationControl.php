<?php

namespace API\Models;

use API\Traits\Updatable;
use Core\Model;

class RoutingOperationControl extends Model
{
    use Updatable;

    const TYPE = "RoutingOperationControl";

    public string $id;
    public string $woTPropertyValue;
    public string $hasRoutingOperation;
    public string $hasWoTProperty;
    public string $hasWorkspace;

    public function toEntity(): Entity
    {
        $entity = new Entity();
        $entity->setId($this->id);
        $entity->setType(self::TYPE);
        $entity->setProperty("woTPropertyValue", $this->woTPropertyValue);
        $entity->setRelationship("hasRoutingOperation", $this->hasRoutingOperation);
        $entity->setRelationship("hasWoTProperty", $this->hasWoTProperty);
        $entity->setRelationship("hasWorkspace", $this->hasWorkspace);
        return $entity;
    }

    public function fromEntity(Entity $entity): void
    {
        $this->id = $entity->getId();
        $this->woTPropertyValue = $entity->getProperty("woTPropertyValue");
        $this->hasRoutingOperation = $entity->getRelationship("hasRoutingOperation");
        $this->hasWoTProperty = $entity->getRelationship("hasWoTProperty");
        $this->hasWorkspace = $entity->getRelationship("hasWorkspace");
    }
}
