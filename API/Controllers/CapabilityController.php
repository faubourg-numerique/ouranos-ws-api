<?php

namespace API\Controllers;

use API\Enums\MimeType;
use API\Managers\CapabilityManager;
use API\Managers\ControlledPropertyManager;
use API\Managers\PropertyManager;
use API\Managers\WorkspaceManager;
use API\Models\Capability;
use API\StaticClasses\Utils;
use Core\API;
use Core\Controller;
use Core\HttpResponseStatusCodes;

class CapabilityController extends Controller
{
    private WorkspaceManager $workspaceManager;
    private CapabilityManager $capabilityManager;
    private ControlledPropertyManager $controlledPropertyManager;
    private PropertyManager $propertyManager;

    public function __construct()
    {
        global $systemEntityManager;
        $this->workspaceManager = new WorkspaceManager($systemEntityManager);
        $this->capabilityManager = new CapabilityManager($systemEntityManager);
        $this->controlledPropertyManager = new ControlledPropertyManager($systemEntityManager);
        $this->propertyManager = new PropertyManager($systemEntityManager);
    }

    public function index(string $workspaceId): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $query = "hasWorkspace==\"{$workspace->id}\"";
        $capabilities = $this->capabilityManager->readMultiple($query);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($capabilities, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function store(string $workspaceId): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $data = API::request()->getDecodedJsonBody();

        $capability = new Capability($data);
        $capability->id = Utils::generateUniqueNgsiLdUrn(Capability::TYPE);

        $this->capabilityManager->create($capability);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_CREATED);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($capability, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function show(string $workspaceId, string $id): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $capability = $this->capabilityManager->readOne($id);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($capability, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function update(string $workspaceId, string $id): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $capability = $this->capabilityManager->readOne($id);

        $data = API::request()->getDecodedJsonBody();

        $capability->update($data);

        $this->capabilityManager->update($capability);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($capability, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function destroy(string $workspaceId, string $id): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $capability = $this->capabilityManager->readOne($id);

        $this->capabilityManager->delete($capability);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_NO_CONTENT);
        API::response()->send();
    }

    public function execute(string $workspaceId, string $id): void
    {
        global $systemEntityManager;

        $workspace = $this->workspaceManager->readOne($workspaceId);
        $capability = $this->capabilityManager->readOne($id);

        $data = API::request()->getDecodedJsonBody();

        if (!isset($data["entityId"], $data["controlledProperties"])) {
            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_BAD_REQUEST);
            API::response()->send();
        }

        $entityController = new EntityController($systemEntityManager);
        $entityManager = $entityController->buildEntityManager($workspace);
        $entity = $entityManager->readOne($data["entityId"]);

        $query = "hasCapability==\"{$capability->id}\"";
        $controlledProperties = $this->controlledPropertyManager->readMultiple($query);

        foreach ($controlledProperties as  $controlledProperty) {
            $property = $this->propertyManager->readOne($controlledProperty->hasProperty);
            $attribute = $data["controlledProperties"][$controlledProperty->id];

            switch ($controlledProperty->capacityType) {
                case "FixedValue": {
                        if ($attribute["value"] != $controlledProperty->capacityValue) {
                            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_BAD_REQUEST);
                            API::response()->send();
                        }
                        break;
                    }
                case "ListOfValues": {
                        $capacityValue = json_decode($controlledProperty->capacityValue, true);
                        if (!in_array($attribute["value"], $capacityValue)) {
                            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_BAD_REQUEST);
                            API::response()->send();
                        }
                        break;
                    }
                case "Range": {
                        $capacityValue = json_decode($controlledProperty->capacityValue, true);
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
