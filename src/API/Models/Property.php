<?php

namespace API\Models;

use API\Traits\Updatable;
use Core\Model;

class Property extends Model
{
    use Updatable;

    const TYPE = "Property";

    public string $id;
    public string $name;
    public ?string $description = null;
    public ?string $url = null;
    public string $ngsiLdType;
    public ?string $propertyNgsiLdValueType = null;
    public ?string $relationshipType = null;
    public ?string $geoPropertyNgsiLdType = null;
    public ?bool $geoPropertyGeographic = null;
    public bool $standard;
    public bool $mandatory;
    public bool $temporal;
    public ?array $temporalServices = null;
    public bool $multiValued;
    public string $hasType;
    public ?string $hasProperty = null;
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
        if (!is_null($this->url)) {
            $entity->setProperty("url", $this->url);
        }
        $entity->setProperty("ngsiLdType", $this->ngsiLdType);
        if (!is_null($this->propertyNgsiLdValueType)) {
            $entity->setProperty("propertyNgsiLdValueType", $this->propertyNgsiLdValueType);
        }
        if (!is_null($this->relationshipType)) {
            $entity->setRelationship("relationshipType", $this->relationshipType);
        }
        if (!is_null($this->geoPropertyNgsiLdType)) {
            $entity->setProperty("geoPropertyNgsiLdType", $this->geoPropertyNgsiLdType);
        }
        if (!is_null($this->geoPropertyGeographic)) {
            $entity->setProperty("geoPropertyGeographic", $this->geoPropertyGeographic);
        }
        $entity->setProperty("standard", $this->standard);
        $entity->setProperty("mandatory", $this->mandatory);
        $entity->setProperty("temporal", $this->temporal);
        if (!is_null($this->temporalServices)) {
            $entity->setProperty("temporalServices", $this->temporalServices);
        }
        $entity->setProperty("multiValued", $this->multiValued);
        $entity->setRelationship("hasType", $this->hasType);
        if (!is_null($this->hasProperty)) {
            $entity->setRelationship("hasProperty", $this->hasProperty);
        }
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
        if ($entity->propertyExists("url")) {
            $this->url = $entity->getProperty("url");
        }
        $this->ngsiLdType = $entity->getProperty("ngsiLdType");
        if ($entity->propertyExists("propertyNgsiLdValueType")) {
            $this->propertyNgsiLdValueType = $entity->getProperty("propertyNgsiLdValueType");
        }
        if ($entity->relationshipExists("relationshipType")) {
            $this->relationshipType = $entity->getRelationship("relationshipType");
        }
        if ($entity->propertyExists("geoPropertyNgsiLdType")) {
            $this->geoPropertyNgsiLdType = $entity->getProperty("geoPropertyNgsiLdType");
        }
        if ($entity->propertyExists("geoPropertyGeographic")) {
            $this->geoPropertyGeographic = $entity->getProperty("geoPropertyGeographic");
        }
        $this->standard = $entity->getProperty("standard");
        $this->mandatory = $entity->getProperty("mandatory");
        $this->temporal = $entity->getProperty("temporal");
        if ($entity->propertyExists("temporalServices")) {
            $this->temporalServices = is_string($entity->getProperty("temporalServices")) ? [$entity->getProperty("temporalServices")] : $entity->getProperty("temporalServices");
        }
        $this->multiValued = $entity->propertyExists("multiValued") ? $entity->getProperty("multiValued") : false;
        $this->hasType = $entity->getRelationship("hasType");
        if ($entity->relationshipExists("hasProperty")) {
            $this->hasProperty = $entity->getRelationship("hasProperty");
        }
        $this->hasWorkspace = $entity->getRelationship("hasWorkspace");
    }
}
