<?php

namespace API\Controllers;

use API\StaticClasses\Utils;
use API\StaticClasses\Validation;
use API\Enums\MimeType;
use API\Exceptions\ControllerException\TypeControllerException;
use API\Managers\PropertyManager;
use API\Managers\TypeManager;
use API\Managers\WorkspaceManager;
use API\Models\Type;
use Core\API;
use Core\Controller;
use Core\HttpResponseStatusCodes;

class TypeController extends Controller
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
        $types = $this->typeManager->readMultiple($query);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($types, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function store(string $workspaceId): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $data = API::request()->getDecodedJsonBody();

        Validation::validateType($data);

        $type = new Type($data);
        $type->id = Utils::generateUniqueNgsiLdUrn(Type::TYPE);

        if ($type->hasWorkspace !== $workspace->id) {
            throw new TypeControllerException\BadWorkspaceException();
        }

        $query = "hasWorkspace==\"{$workspace->id}\";name==\"{$type->name}\"";
        $types = $this->typeManager->readMultiple($query);

        if (in_array($type->name, $types, true)) {
            throw new TypeControllerException\NameAlreadyUsedException();
        }

        $query = "hasWorkspace==\"{$workspace->id}\";name==\"{$type->name}\"";
        $properties = $this->propertyManager->readMultiple($query);
        $types = $this->typeManager->readMultiple($query);
        $elements = array_merge($properties, $types);

        foreach ($elements as $element) {
            if ($type->id === $element->id) continue;
            if ($type->url === $element->url) continue;
            throw new TypeControllerException\UrlInvalidException();
        }

        $this->typeManager->create($type);

        $workspace->dataModelUpToDate = false;
        $this->workspaceManager->update($workspace);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_CREATED);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($type, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function show(string $workspaceId, string $id): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $type = $this->typeManager->readOne($id);

        if ($type->hasWorkspace !== $workspace->id) {
            throw new TypeControllerException\BadWorkspaceException();
        }

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($type, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function update(string $workspaceId, string $id): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $type = $this->typeManager->readOne($id);

        if ($type->hasWorkspace !== $workspace->id) {
            throw new TypeControllerException\BadWorkspaceException();
        }

        $data = API::request()->getDecodedJsonBody();

        Validation::validateType($data);

        if ($data["name"] !== $type->name) {
            $query = "hasWorkspace==\"{$workspace->id}\";name==\"{$type->name}\"";
            $types = $this->typeManager->readMultiple($query);

            if (in_array($type->name, $types, true)) {
                throw new TypeControllerException\NameAlreadyUsedException();
            }
        }

        $type->update($data);

        if ($type->hasWorkspace !== $workspace->id) {
            throw new TypeControllerException\BadWorkspaceException();
        }

        $query = "hasWorkspace==\"{$workspace->id}\";name==\"{$type->name}\"";
        $properties = $this->propertyManager->readMultiple($query);
        $types = $this->typeManager->readMultiple($query);
        $elements = array_merge($properties, $types);

        foreach ($elements as $element) {
            if ($type->id === $element->id) continue;
            if ($type->url === $element->url) continue;
            throw new TypeControllerException\UrlInvalidException();
        }

        $this->typeManager->update($type);

        $workspace->dataModelUpToDate = false;
        $this->workspaceManager->update($workspace);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($type, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function destroy(string $workspaceId, string $id): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $type = $this->typeManager->readOne($id);

        if ($type->hasWorkspace !== $workspace->id) {
            throw new TypeControllerException\BadWorkspaceException();
        }

        $query = "hasType==\"{$type->id}\"";
        $properties = $this->propertyManager->readMultiple($query);

        if ($properties) {
            throw new TypeControllerException\RelationshipException();
        }

        $this->typeManager->delete($type);

        $workspace->dataModelUpToDate = false;
        $this->workspaceManager->update($workspace);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_NO_CONTENT);
        API::response()->send();
    }

    public function updatePositionInChart(string $workspaceId, string $id): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $type = $this->typeManager->readOne($id);

        if ($type->hasWorkspace !== $workspace->id) {
            throw new TypeControllerException\BadWorkspaceException();
        }

        $data = API::request()->getDecodedJsonBody();

        Validation::validatePositionInChart($data);

        $type->positionInChart = $data;

        $this->typeManager->update($type);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->send();
    }
}
