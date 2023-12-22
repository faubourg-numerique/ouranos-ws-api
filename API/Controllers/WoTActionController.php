<?php

namespace API\Controllers;

use API\Enums\MimeType;
use API\Managers\WoTActionManager;
use API\Managers\WoTPropertyManager;
use API\Managers\PropertyManager;
use API\Managers\WorkspaceManager;
use API\Models\WoTAction;
use API\StaticClasses\Utils;
use Core\API;
use Core\Controller;
use Core\HttpResponseStatusCodes;

class WoTActionController extends Controller
{
    private WorkspaceManager $workspaceManager;
    private WoTActionManager $woTActionManager;
    private WoTPropertyManager $woTPropertyManager;
    private PropertyManager $propertyManager;

    public function __construct()
    {
        global $systemEntityManager;
        $this->workspaceManager = new WorkspaceManager($systemEntityManager);
        $this->woTActionManager = new WoTActionManager($systemEntityManager);
        $this->woTPropertyManager = new WoTPropertyManager($systemEntityManager);
        $this->propertyManager = new PropertyManager($systemEntityManager);
    }

    public function index(string $workspaceId): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $query = "hasWorkspace==\"{$workspace->id}\"";
        $woTActions = $this->woTActionManager->readMultiple($query);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($woTActions, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function store(string $workspaceId): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $data = API::request()->getDecodedJsonBody();

        $woTAction = new WoTAction($data);
        $woTAction->id = Utils::generateUniqueNgsiLdUrn(WoTAction::TYPE);

        $this->woTActionManager->create($woTAction);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_CREATED);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($woTAction, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function show(string $workspaceId, string $id): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $woTAction = $this->woTActionManager->readOne($id);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($woTAction, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function update(string $workspaceId, string $id): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $woTAction = $this->woTActionManager->readOne($id);

        $data = API::request()->getDecodedJsonBody();

        $woTAction->update($data);

        $this->woTActionManager->update($woTAction);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($woTAction, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function destroy(string $workspaceId, string $id): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $woTAction = $this->woTActionManager->readOne($id);

        $this->woTActionManager->delete($woTAction);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_NO_CONTENT);
        API::response()->send();
    }

    public function execute(string $workspaceId, string $id): void
    {
        global $systemEntityManager;

        $workspace = $this->workspaceManager->readOne($workspaceId);
        $woTAction = $this->woTActionManager->readOne($id);

        $data = API::request()->getDecodedJsonBody();

        if (!isset($data["entityId"], $data["woTProperties"])) {
            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_BAD_REQUEST);
            API::response()->send();
        }

        $entityController = new EntityController($systemEntityManager);
        $entityManager = $entityController->buildEntityManager($workspace);
        $entity = $entityManager->readOne($data["entityId"]);

        $query = "hasWoTAction==\"{$woTAction->id}\"";
        $woTProperties = $this->woTPropertyManager->readMultiple($query);

        foreach ($woTProperties as  $woTProperty) {
            $property = $this->propertyManager->readOne($woTProperty->hasProperty);
            $attribute = $data["woTProperties"][$woTProperty->id];

            switch ($woTProperty->capacityType) {
                case "FixedValue": {
                        if ($attribute["value"] != $woTProperty->capacityValue) {
                            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_BAD_REQUEST);
                            API::response()->send();
                        }
                        break;
                    }
                case "ListOfValues": {
                        $capacityValue = json_decode($woTProperty->capacityValue, true);
                        if (!in_array($attribute["value"], $capacityValue)) {
                            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_BAD_REQUEST);
                            API::response()->send();
                        }
                        break;
                    }
                case "Range": {
                        $capacityValue = json_decode($woTProperty->capacityValue, true);
                        if ($attribute["value"] < $capacityValue[0] || $attribute["value"] > $capacityValue[1]) {
                            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_BAD_REQUEST);
                            API::response()->send();
                        }
                        break;
                    }
                case "FreeText": {
                        break;
                    }
                default: {
                        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_BAD_REQUEST);
                        API::response()->send();
                        break;
                    }
            }

            $propertyName = $property->name;
            $entity->$propertyName = $attribute;
        }

        $entityManager->update($entity);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_NO_CONTENT);
        API::response()->send();
    }
}
