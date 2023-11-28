<?php

namespace API\Controllers;

use API\Enums\MimeType;
use API\Managers\ControlledPropertyManager;
use API\Managers\WorkspaceManager;
use API\Models\ControlledProperty;
use API\StaticClasses\Utils;
use Core\API;
use Core\Controller;
use Core\HttpResponseStatusCodes;

class ControlledPropertyController extends Controller
{
    private WorkspaceManager $workspaceManager;
    private ControlledPropertyManager $controlledPropertyManager;

    public function __construct()
    {
        global $systemEntityManager;
        $this->workspaceManager = new WorkspaceManager($systemEntityManager);
        $this->controlledPropertyManager = new ControlledPropertyManager($systemEntityManager);
    }

    public function index(string $workspaceId): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $query = "hasWorkspace==\"{$workspace->id}\"";
        $controlledProperties = $this->controlledPropertyManager->readMultiple($query);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($controlledProperties, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function store(string $workspaceId): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $data = API::request()->getDecodedJsonBody();

        $controlledProperty = new ControlledProperty($data);
        $controlledProperty->id = Utils::generateUniqueNgsiLdUrn(ControlledProperty::TYPE);

        $this->controlledPropertyManager->create($controlledProperty);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_CREATED);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($controlledProperty, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function show(string $workspaceId, string $id): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $controlledProperty = $this->controlledPropertyManager->readOne($id);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($controlledProperty, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function update(string $workspaceId, string $id): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $controlledProperty = $this->controlledPropertyManager->readOne($id);

        $data = API::request()->getDecodedJsonBody();

        $controlledProperty->update($data);

        $this->controlledPropertyManager->update($controlledProperty);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($controlledProperty, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function destroy(string $workspaceId, string $id): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $controlledProperty = $this->controlledPropertyManager->readOne($id);

        $this->controlledPropertyManager->delete($controlledProperty);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_NO_CONTENT);
        API::response()->send();
    }
}
