<?php

namespace API\Models;

use API\Traits\Updatable;
use Core\Model;

class RoutingOperation extends Model
{
    use Updatable;

    const TYPE = "RoutingOperation";

    public string $id;
    public int $sequenceNumber;
    public string $hasRouting;
    public string $hasCapability;
    public int $timerBefore;
    public int $timerAfter;
    public string $hasWorkspace;

    public function toEntity(): Entity
    {
        $entity = new Entity();
        $entity->setId($this->id);
        $entity->setType(self::TYPE);
        $entity->setProperty("sequenceNumber", $this->sequenceNumber);
        $entity->setRelationship("hasRouting", $this->hasRouting);
        $entity->setRelationship("hasCapability", $this->hasCapability);
        $entity->setProperty("timerBefore", $this->timerBefore);
        $entity->setProperty("timerAfter", $this->timerAfter);
        $entity->setRelationship("hasWorkspace", $this->hasWorkspace);
        return $entity;
    }

    public function fromEntity(Entity $entity): void
    {
        $this->id = $entity->getId();
        $this->sequenceNumber = $entity->getProperty("sequenceNumber");
        $this->hasRouting = $entity->getRelationship("hasRouting");
        $this->hasCapability = $entity->getRelationship("hasCapability");
        $this->timerBefore = $entity->getProperty("timerBefore");
        $this->timerAfter = $entity->getProperty("timerAfter");
        $this->hasWorkspace = $entity->getRelationship("hasWorkspace");
    }
}
