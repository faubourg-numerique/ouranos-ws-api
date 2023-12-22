<?php

namespace API\Controllers;

use API\Enums\MimeType;
use API\Managers\WoTActionParameterManager;
use API\Managers\WorkspaceManager;
use API\Models\WoTActionParameter;
use API\StaticClasses\Utils;
use Core\API;
use Core\Controller;
use Core\HttpResponseStatusCodes;

class WoTActionParameterController extends Controller
{
    private WorkspaceManager $workspaceManager;
    private WoTActionParameterManager $woTActionParameterManager;

    public function __construct()
    {
        global $systemEntityManager;
        $this->workspaceManager = new WorkspaceManager($systemEntityManager);
        $this->woTActionParameterManager = new WoTActionParameterManager($systemEntityManager);
    }

    public function index(string $workspaceId): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $query = "hasWorkspace==\"{$workspace->id}\"";
        $woTActionParameters = $this->woTActionParameterManager->readMultiple($query);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($woTActionParameters, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function store(string $workspaceId): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $data = API::request()->getDecodedJsonBody();

        $woTActionParameter = new WoTActionParameter($data);
        $woTActionParameter->id = Utils::generateUniqueNgsiLdUrn(WoTActionParameter::TYPE);

        $this->woTActionParameterManager->create($woTActionParameter);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_CREATED);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($woTActionParameter, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function show(string $workspaceId, string $id): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $woTActionParameter = $this->woTActionParameterManager->readOne($id);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($woTActionParameter, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function update(string $workspaceId, string $id): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $woTActionParameter = $this->woTActionParameterManager->readOne($id);

        $data = API::request()->getDecodedJsonBody();

        $woTActionParameter->update($data);

        $this->woTActionParameterManager->update($woTActionParameter);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($woTActionParameter, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function destroy(string $workspaceId, string $id): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $woTActionParameter = $this->woTActionParameterManager->readOne($id);

        $this->woTActionParameterManager->delete($woTActionParameter);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_NO_CONTENT);
        API::response()->send();
    }
}
