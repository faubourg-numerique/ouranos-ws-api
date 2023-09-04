<?php

namespace API\Controllers;

use API\StaticClasses\Utils;
use API\StaticClasses\Validation;
use API\Enums\MimeType;
use API\Exceptions\ControllerException\PropertyControllerException;
use API\Managers\PropertyManager;
use API\Managers\TypeManager;
use API\Managers\WorkspaceManager;
use API\Models\Property;
use Core\API;
use Core\Controller;
use Core\HttpResponseStatusCodes;

class PropertyController extends Controller
{
    private WorkspaceManager $workspaceManager;
    private TypeManager $typeManager;
    private PropertyManager $propertyManager;

    public function __construct()
    {
        global $systemEntityManager;
        $this->workspaceManager = new WorkspaceManager($systemEntityManager);
        $this->typeManager = new TypeManager($systemEntityManager);
        $this->propertyManager = new PropertyManager($systemEntityManager);
    }

    public function index(string $workspaceId): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $query = "hasWorkspace==\"{$workspace->id}\"";
        $properties = $this->propertyManager->readMultiple($query);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($properties, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function store(string $workspaceId): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $data = API::request()->getDecodedJsonBody();

        Validation::validateProperty($data);

        $property = new Property($data);
        $property->id = Utils::generateUniqueNgsiLdUrn(Property::TYPE);

        if ($property->hasWorkspace !== $workspace->id) {
            throw new PropertyControllerException\BadWorkspaceException();
        }

        $query = "hasWorkspace==\"{$workspace->id}\";name==\"{$property->name}\"";
        $properties = $this->propertyManager->readMultiple($query);
        $types = $this->typeManager->readMultiple($query);
        $elements = array_merge($properties, $types);

        foreach ($elements as $element) {
            if ($property->id === $element->id) continue;
            if ($property->url === $element->url) continue;
            throw new PropertyControllerException\UrlInvalidException();
        }

        $this->propertyManager->create($property);

        $workspace->dataModelUpToDate = false;
        $this->workspaceManager->update($workspace);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_CREATED);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($property, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function show(string $workspaceId, string $id): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $property = $this->propertyManager->readOne($id);

        if ($property->hasWorkspace !== $workspace->id) {
            throw new PropertyControllerException\BadWorkspaceException();
        }

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($property, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function update(string $workspaceId, string $id): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $property = $this->propertyManager->readOne($id);

        if ($property->hasWorkspace !== $workspace->id) {
            throw new PropertyControllerException\BadWorkspaceException();
        }

        $data = API::request()->getDecodedJsonBody();

        Validation::validateProperty($data);

        $property->update($data);

        if ($property->hasWorkspace !== $workspace->id) {
            throw new PropertyControllerException\BadWorkspaceException();
        }

        $query = "hasWorkspace==\"{$workspace->id}\";name==\"{$property->name}\"";
        $properties = $this->propertyManager->readMultiple($query);
        $types = $this->typeManager->readMultiple($query);
        $elements = array_merge($properties, $types);

        foreach ($elements as $element) {
            if ($property->id === $element->id) continue;
            if ($property->url === $element->url) continue;
            throw new PropertyControllerException\UrlInvalidException();
        }

        $this->propertyManager->update($property);

        $workspace->dataModelUpToDate = false;
        $this->workspaceManager->update($workspace);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($property, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function destroy(string $workspaceId, string $id): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $property = $this->propertyManager->readOne($id);

        if ($property->hasWorkspace !== $workspace->id) {
            throw new PropertyControllerException\BadWorkspaceException();
        }

        $query = "hasProperty==\"{$property->id}\"";
        $properties = $this->propertyManager->readMultiple($query);

        if ($properties) {
            throw new PropertyControllerException\RelationshipException();
        }

        $this->propertyManager->delete($property);

        $workspace->dataModelUpToDate = false;
        $this->workspaceManager->update($workspace);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_NO_CONTENT);
        API::response()->send();
    }
}
