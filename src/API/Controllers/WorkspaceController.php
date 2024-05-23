<?php

namespace API\Controllers;

use API\StaticClasses\Utils;
use API\StaticClasses\Validation;
use API\Enums\MimeType;
use API\Exceptions\ControllerException\WorkspaceControllerException;
use API\Managers\TypeManager;
use API\Managers\WorkspaceManager;
use API\Models\Workspace;
use Core\API;
use Core\Controller;
use Core\HttpResponseStatusCodes;

class WorkspaceController extends Controller
{
    private WorkspaceManager $workspaceManager;
    private TypeManager $typeManager;

    public function __construct()
    {
        global $systemEntityManager;
        $this->workspaceManager = new WorkspaceManager($systemEntityManager);
        $this->typeManager = new TypeManager($systemEntityManager);
    }

    public function index(): void
    {
        $workspaces = $this->workspaceManager->readMultiple();

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($workspaces, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function store(): void
    {
        $data = API::request()->getDecodedJsonBody();

        Validation::validateWorkspace($data);

        $workspace = new Workspace($data);
        $workspace->id = Utils::generateUniqueNgsiLdUrn(Workspace::TYPE);
        $this->workspaceManager->create($workspace);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_CREATED);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($workspace, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function show(string $id): void
    {
        $workspace = $this->workspaceManager->readOne($id);

        if (is_null($workspace)) {
            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_NOT_FOUND);
            API::response()->send();
        }

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($workspace, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function update(string $id): void
    {
        $workspace = $this->workspaceManager->readOne($id);

        $data = API::request()->getDecodedJsonBody();

        Validation::validateWorkspace($data);

        $data["dataModelUpToDate"] = $workspace->dataModelUpToDate;

        $workspace->update($data);

        $this->workspaceManager->update($workspace);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($workspace, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function destroy(string $id): void
    {
        $workspace = $this->workspaceManager->readOne($id);

        $query = "hasWorkspace==\"{$workspace->id}\"";
        $types = $this->typeManager->readMultiple($query);

        if ($types) {
            throw new WorkspaceControllerException\RelationshipException();
        }

        $this->workspaceManager->delete($workspace);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_NO_CONTENT);
        API::response()->send();
    }
}
