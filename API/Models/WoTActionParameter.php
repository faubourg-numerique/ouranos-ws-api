<?php

namespace API\Models;

use API\Traits\Updatable;
use Core\Model;

class WoTActionParameter extends Model
{
    use Updatable;

    const TYPE = "WoTActionParameter";

    public string $id;
    public string $hasWoTAction;
    public string $hasWoTProperty;
    public string $hasWorkspace;

    public function toEntity(): Entity
    {
        $entity = new Entity();
        $entity->setId($this->id);
        $entity->setType(self::TYPE);
        $entity->setRelationship("hasWoTAction", $this->hasWoTAction);
        $entity->setRelationship("hasWoTProperty", $this->hasWoTProperty);
        $entity->setRelationship("hasWorkspace", $this->hasWorkspace);
        return $entity;
    }

    public function fromEntity(Entity $entity): void
    {
        $this->id = $entity->getId();
        $this->hasWoTAction = $entity->getRelationship("hasWoTAction");
        $this->hasWoTProperty = $entity->getRelationship("hasWoTProperty");
        $this->hasWorkspace = $entity->getRelationship("hasWorkspace");
    }
}
