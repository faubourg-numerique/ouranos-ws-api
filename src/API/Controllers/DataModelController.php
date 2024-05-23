<?php

namespace API\Controllers;

use API\Enums\NgsiLdPropertyValueType;
use API\StaticClasses\DataModel;
use API\Managers\PropertyManager;
use API\Managers\TypeManager;
use API\Managers\WorkspaceManager;
use API\Models\Property;
use API\StaticClasses\Utils;
use Core\API;
use Core\Controller;
use Core\HttpResponseStatusCodes;
use Exception;

class DataModelController extends Controller
{
    private WorkspaceManager $workspaceManager;
    private TypeManager $typeManager;
    private PropertyManager $propertyManager;
    private EntityController $entityController;

    public function __construct()
    {
        global $systemEntityManager;
        $this->workspaceManager = new WorkspaceManager($systemEntityManager);
        $this->typeManager = new TypeManager($systemEntityManager);
        $this->propertyManager = new PropertyManager($systemEntityManager);
        $this->entityController = new EntityController();
    }

    public function generate(string $workspaceId): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        DataModel::generate($workspace, $this->propertyManager, $this->typeManager, $this->workspaceManager);

        $workspace->dataModelUpToDate = true;
        $this->workspaceManager->update($workspace);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_NO_CONTENT);
        API::response()->send();
    }

    public function autoDiscover(string $workspaceId): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);
        $entityManager = $this->entityController->buildEntityManager($workspace);

        $query = "hasWorkspace==\"{$workspace->id}\"";
        $types = $this->typeManager->readMultiple($query);

        foreach ($types as $type) {
            $entities = $entityManager->readMultiple(null, $type->name);

            foreach ($entities as $entity) {
                $data = (array) $entity;

                foreach ($data as $name => $attribute) {
                    if (!$name || in_array($name, ["id", "type"])) {
                        continue;
                    }

                    if (!$entity->isAttributeValid($attribute)) {
                        continue;
                    }

                    $property = new Property();

                    if (filter_var($name, FILTER_VALIDATE_URL)) {
                        $property->url = $name;

                        $parts = preg_split("/[^a-zA-Z0-9]+/", $name);
                        if (!$parts || count($parts) < 2) {
                            continue;
                        }

                        $property->name = end($parts);
                    } else {
                        $property->name = $name;
                    }

                    if (!$property->name) {
                        continue;
                    }

                    $property->id = Utils::generateUniqueNgsiLdUrn(Property::TYPE);
                    $property->ngsiLdType = $attribute["type"];
                    $property->standard = false;
                    $property->mandatory = false;
                    $property->temporal = false;
                    $property->multiValued = false;
                    $property->hasType = $type->id;
                    $property->hasWorkspace = $workspace->id;

                    if ($entity->isAttributeProperty($attribute)) {
                        switch (gettype($attribute["value"])) {
                            case "string": {
                                    $property->propertyNgsiLdValueType = NgsiLdPropertyValueType::String->value;
                                    break;
                                }
                            case "integer":
                            case "double": {
                                    $property->propertyNgsiLdValueType = NgsiLdPropertyValueType::Number->value;
                                    break;
                                }
                            case "boolean": {
                                    $property->propertyNgsiLdValueType = NgsiLdPropertyValueType::Boolean->value;
                                    break;
                                }
                            case "array": {
                                    $property->propertyNgsiLdValueType = NgsiLdPropertyValueType::Object->value;
                                    break;
                                }
                            default: {
                                    $property->propertyNgsiLdValueType = NgsiLdPropertyValueType::String->value;
                                    break;
                                }
                        }
                    } elseif ($entity->isAttributeGeoProperty($attribute)) {
                        $property->geoPropertyNgsiLdType = $attribute["value"]["type"];
                        $property->geoPropertyGeographic = true;
                    } elseif ($entity->isAttributeRelationship($attribute)) {
                        $typeName = @Utils::extractTypeFromNgsiLdUrn($attribute["object"]);

                        if (!$typeName) {
                            continue;
                        }

                        $query = "hasWorkspace==\"{$workspace->id}\";name==\"{$typeName}\"";
                        $_types = $this->typeManager->readMultiple($query);

                        if (!$_types) {
                            continue;
                        }

                        $property->relationshipType = $_types[0]->id;
                    }

                    $query = "hasWorkspace==\"{$workspace->id}\";hasType==\"{$type->id}\";name==\"{$property->name}\"";
                    $_properties = $this->propertyManager->readMultiple($query);

                    if ($_properties) {
                        continue;
                    }

                    $query = "hasWorkspace==\"{$workspace->id}\";name==\"{$property->name}\"";
                    $_properties = $this->propertyManager->readMultiple($query);
                    $_types = $this->typeManager->readMultiple($query);
                    $elements = array_merge($_properties, $_types);

                    foreach ($elements as $element) {
                        if ($property->url !== $element->url) {
                            continue 2;
                        }
                    }

                    try {
                        $this->propertyManager->create($property);
                    } catch (Exception $exception) {
                    }
                }
            }
        }

        $workspace->dataModelUpToDate = false;
        $this->workspaceManager->update($workspace);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_NO_CONTENT);
        API::response()->send();
    }
}
