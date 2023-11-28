<?php

namespace API\Controllers;

use API\Enums\MimeType;
use API\Managers\RoutingOperationControlManager;
use API\Managers\WorkspaceManager;
use API\Models\RoutingOperationControl;
use API\StaticClasses\Utils;
use Core\API;
use Core\Controller;
use Core\HttpResponseStatusCodes;

class RoutingOperationControlController extends Controller
{
    private WorkspaceManager $workspaceManager;
    private RoutingOperationControlManager $routingOperationControlManager;

    public function __construct()
    {
        global $systemEntityManager;
        $this->workspaceManager = new WorkspaceManager($systemEntityManager);
        $this->routingOperationControlManager = new RoutingOperationControlManager($systemEntityManager);
    }

    public function index(string $workspaceId): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $query = "hasWorkspace==\"{$workspace->id}\"";
        $routingOperationControls = $this->routingOperationControlManager->readMultiple($query);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($routingOperationControls, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function store(string $workspaceId): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $data = API::request()->getDecodedJsonBody();

        $routingOperationControl = new RoutingOperationControl($data);
        $routingOperationControl->id = Utils::generateUniqueNgsiLdUrn(RoutingOperationControl::TYPE);

        $this->routingOperationControlManager->create($routingOperationControl);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_CREATED);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($routingOperationControl, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function show(string $workspaceId, string $id): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $routingOperationControl = $this->routingOperationControlManager->readOne($id);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($routingOperationControl, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function update(string $workspaceId, string $id): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $routingOperationControl = $this->routingOperationControlManager->readOne($id);

        $data = API::request()->getDecodedJsonBody();

        $routingOperationControl->update($data);

        $this->routingOperationControlManager->update($routingOperationControl);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($routingOperationControl, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function destroy(string $workspaceId, string $id): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $routingOperationControl = $this->routingOperationControlManager->readOne($id);

        $this->routingOperationControlManager->delete($routingOperationControl);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_NO_CONTENT);
        API::response()->send();
    }
}
