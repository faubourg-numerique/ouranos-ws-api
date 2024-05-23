<?php

namespace API\Models;

use API\Traits\Updatable;
use Core\Model;

class Type extends Model
{
    use Updatable;

    const TYPE = "Type";

    public string $id;
    public string $name;
    public ?string $description = null;
    public ?string $url = null;
    public bool $standardDataModelBased;
    public ?string $standardDataModelType = null;
    public ?string $standardDataModelDefinitionUrl = null;
    public ?array $positionInChart = null;
    public ?string $dataModelGroup = null;
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
        $entity->setProperty("standardDataModelBased", $this->standardDataModelBased);
        if (!is_null($this->standardDataModelType)) {
            $entity->setProperty("standardDataModelType", $this->standardDataModelType);
        }
        if (!is_null($this->standardDataModelDefinitionUrl)) {
            $entity->setProperty("standardDataModelDefinitionUrl", $this->standardDataModelDefinitionUrl);
        }
        if (!is_null($this->positionInChart)) {
            $entity->setProperty("positionInChart", $this->positionInChart);
        }
        if (!is_null($this->dataModelGroup)) {
            $entity->setProperty("dataModelGroup", $this->dataModelGroup);
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
        $this->standardDataModelBased = $entity->getProperty("standardDataModelBased");
        if ($entity->propertyExists("standardDataModelType")) {
            $this->standardDataModelType = $entity->getProperty("standardDataModelType");
        }
        if ($entity->propertyExists("standardDataModelDefinitionUrl")) {
            $this->standardDataModelDefinitionUrl = $entity->getProperty("standardDataModelDefinitionUrl");
        }
        if ($entity->propertyExists("positionInChart")) {
            $this->positionInChart = $entity->getProperty("positionInChart");
        }
        if ($entity->propertyExists("dataModelGroup")) {
            $this->dataModelGroup = $entity->getProperty("dataModelGroup");
        }
        $this->dataModelGroup = $entity->getProperty("dataModelGroup");
        $this->hasWorkspace = $entity->getRelationship("hasWorkspace");
    }
}
