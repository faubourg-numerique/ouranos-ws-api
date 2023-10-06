<?php

namespace API\Models;

use API\Traits\Updatable;
use Core\Model;

class Service extends Model
{
    use Updatable;

    const TYPE = "Service";

    public string $id;
    public string $name;
    public ?string $description = null;
    public bool $authorizationRequired;
    public string $hasContextBroker;
    public ?string $hasIdentityManager = null;
    public ?string $hasIdentityManagerGrant = null;

    public function toEntity(): Entity
    {
        $entity = new Entity();
        $entity->setId($this->id);
        $entity->setType(self::TYPE);
        $entity->setProperty("name", $this->name);
        if (!is_null($this->description)) {
            $entity->setProperty("description", $this->description);
        }
        $entity->setProperty("authorizationRequired", $this->authorizationRequired);
        if (!is_null($this->authorizationMode)) {
            $entity->setRelationship("authorizationMode", $this->authorizationMode);
        }
        $entity->setRelationship("hasContextBroker", $this->hasContextBroker);
        if (!is_null($this->hasIdentityManager)) {
            $entity->setRelationship("hasIdentityManager", $this->hasIdentityManager);
        }
        if (!is_null($this->hasIdentityManagerGrant)) {
            $entity->setRelationship("hasIdentityManagerGrant", $this->hasIdentityManagerGrant);
        }
        if (!is_null($this->hasVCVerifier)) {
            $entity->setRelationship("hasVCVerifier", $this->hasVCVerifier);
        }
        return $entity;
    }

    public function fromEntity(Entity $entity): void
    {
        $this->id = $entity->getId();
        $this->name = $entity->getProperty("name");
        if ($entity->propertyExists("description")) {
            $this->description = $entity->getProperty("description");
        }
        $this->authorizationRequired = $entity->getProperty("authorizationRequired");
        if ($entity->relationshipExists("authorizationMode")) {
            $this->authorizationMode = $entity->getRelationship("authorizationMode");
        }
        $this->hasContextBroker = $entity->getRelationship("hasContextBroker");
        if ($entity->relationshipExists("hasIdentityManager")) {
            $this->hasIdentityManager = $entity->getRelationship("hasIdentityManager");
        }
        if ($entity->relationshipExists("hasIdentityManagerGrant")) {
            $this->hasIdentityManagerGrant = $entity->getRelationship("hasIdentityManagerGrant");
        }
        if ($entity->relationshipExists("hasVCVerifier")) {
            $this->hasVCVerifier = $entity->getRelationship("hasVCVerifier");
        }
    }
}
