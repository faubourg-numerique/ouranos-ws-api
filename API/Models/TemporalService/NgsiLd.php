<?php

namespace API\Models\TemporalService;

use API\Models\Entity;
use API\Models\TemporalService;

class NgsiLd extends TemporalService
{
    public function toEntity(): Entity
    {
        $entity = new Entity();
        $entity->setId($this->id);
        $entity->setType(self::TYPE);
        $entity->setProperty("name", $this->name);
        if (!is_null($this->description)) {
            $entity->setProperty("description", $this->description);
        }
        $entity->setProperty("temporalServiceType", $this->temporalServiceType);
        return $entity;
    }

    public function fromEntity(Entity $entity): void
    {
        $this->id = $entity->getId();
        $this->name = $entity->getProperty("name");
        if ($entity->propertyExists("description")) {
            $this->description = $entity->getProperty("description");
        }
        $this->temporalServiceType = $entity->getProperty("temporalServiceType");
    }
}
