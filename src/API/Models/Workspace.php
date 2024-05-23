<?php

namespace API\Models;

use API\StaticClasses\Utils;
use API\Traits\Updatable;
use Core\Model;

class Workspace extends Model
{
    use Updatable;

    const TYPE = "Workspace";

    public string $id;
    public string $name;
    public ?string $description = null;
    public string $dataModelName;
    public int $dataModelVersion;
    public bool $dataModelUpToDate = false;
    public ?string $contextBrokerTenant = null;
    public ?string $defaultDataModelUrl = null;
    public array $temporalServices;
    public string $hasService;
    public bool $enableOffers;
    public ?string $hasAuthorizationRegistry = null;
    public ?string $hasAuthorizationRegistryGrant = null;

    public function getContextUrl(): string
    {
        $path = (isset($_ENV["DATA_MODELS_PATH"]) ? $_ENV["DATA_MODELS_PATH"] : null) . "/{$this->dataModelName}/{$this->dataModelVersion}/context.jsonld";
        return Utils::buildUrl($_ENV["DATA_MODELS_SCHEME"], $_ENV["DATA_MODELS_HOST"], $_ENV["DATA_MODELS_PORT"], $path);
    }

    public function toEntity(): Entity
    {
        $entity = new Entity();
        $entity->setId($this->id);
        $entity->setType(self::TYPE);
        $entity->setProperty("name", $this->name);
        if (!is_null($this->description)) {
            $entity->setProperty("description", $this->description);
        }
        $entity->setProperty("dataModelName", $this->dataModelName);
        $entity->setProperty("dataModelVersion", $this->dataModelVersion);
        $entity->setProperty("dataModelUpToDate", $this->dataModelUpToDate);
        if (!is_null($this->contextBrokerTenant)) {
            $entity->setProperty("contextBrokerTenant", $this->contextBrokerTenant);
        }
        if (!is_null($this->defaultDataModelUrl)) {
            $entity->setProperty("defaultDataModelUrl", $this->defaultDataModelUrl);
        }
        $entity->setProperty("temporalServices", $this->temporalServices);
        $entity->setRelationship("hasService", $this->hasService);
        $entity->setProperty("enableOffers", $this->enableOffers);
        if (!is_null($this->hasAuthorizationRegistry)) {
            $entity->setRelationship("hasAuthorizationRegistry", $this->hasAuthorizationRegistry);
        }
        if (!is_null($this->hasAuthorizationRegistryGrant)) {
            $entity->setRelationship("hasAuthorizationRegistryGrant", $this->hasAuthorizationRegistryGrant);
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
        $this->dataModelName = $entity->getProperty("dataModelName");
        $this->dataModelVersion = $entity->getProperty("dataModelVersion");
        $this->dataModelUpToDate = boolval($entity->getProperty("dataModelUpToDate"));
        if ($entity->propertyExists("contextBrokerTenant")) {
            $this->contextBrokerTenant = $entity->getProperty("contextBrokerTenant");
        }
        if ($entity->propertyExists("defaultDataModelUrl")) {
            $this->defaultDataModelUrl = $entity->getProperty("defaultDataModelUrl");
        }
        $this->temporalServices = !is_array($entity->getProperty("temporalServices")) ? [$entity->getProperty("temporalServices")] : $entity->getProperty("temporalServices");
        $this->hasService = $entity->getRelationship("hasService");
        $this->enableOffers = $entity->propertyExists("enableOffers") ? $entity->getProperty("enableOffers") : false;
        if ($entity->relationshipExists("hasAuthorizationRegistry")) {
            $this->hasAuthorizationRegistry = $entity->getRelationship("hasAuthorizationRegistry");
        }
        if ($entity->relationshipExists("hasAuthorizationRegistryGrant")) {
            $this->hasAuthorizationRegistryGrant = $entity->getRelationship("hasAuthorizationRegistryGrant");
        }
    }
}
