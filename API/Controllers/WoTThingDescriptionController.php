<?php

namespace API\Controllers;

use API\Enums\MimeType;
use API\Managers\WoTThingDescriptionManager;
use API\Managers\WorkspaceManager;
use API\Models\WoTThingDescription;
use API\StaticClasses\Utils;
use Core\API;
use Core\Controller;
use Core\HttpResponseStatusCodes;

class WoTThingDescriptionController extends Controller
{
    private WorkspaceManager $workspaceManager;
    private WoTThingDescriptionManager $woTThingDescriptionManager;

    public function __construct()
    {
        global $systemEntityManager;
        $this->workspaceManager = new WorkspaceManager($systemEntityManager);
        $this->woTThingDescriptionManager = new WoTThingDescriptionManager($systemEntityManager);
    }

    public function index(string $workspaceId): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $query = "hasWorkspace==\"{$workspace->id}\"";
        $woTThingDescriptions = $this->woTThingDescriptionManager->readMultiple($query);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($woTThingDescriptions, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function store(string $workspaceId): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $data = API::request()->getDecodedJsonBody();

        $woTThingDescription = new WoTThingDescription($data);
        $woTThingDescription->id = Utils::generateUniqueNgsiLdUrn(WoTThingDescription::TYPE);

        $this->woTThingDescriptionManager->create($woTThingDescription);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_CREATED);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($woTThingDescription, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function show(string $workspaceId, string $id): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $woTThingDescription = $this->woTThingDescriptionManager->readOne($id);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($woTThingDescription, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function update(string $workspaceId, string $id): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $woTThingDescription = $this->woTThingDescriptionManager->readOne($id);

        $data = API::request()->getDecodedJsonBody();

        $woTThingDescription->update($data);

        $this->woTThingDescriptionManager->update($woTThingDescription);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($woTThingDescription, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function destroy(string $workspaceId, string $id): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $woTThingDescription = $this->woTThingDescriptionManager->readOne($id);

        $this->woTThingDescriptionManager->delete($woTThingDescription);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_NO_CONTENT);
        API::response()->send();
    }
}
