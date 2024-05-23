<?php

namespace API\Controllers;

use API\Enums\MimeType;
use API\Enums\NgsiLdPropertyValueType;
use API\Managers\PropertyManager;
use API\Managers\WoTThingDescriptionManager;
use API\Managers\WoTPropertyManager;
use API\Managers\WoTActionManager;
use API\Managers\WoTEventManager;
use API\Managers\WorkspaceManager;
use API\Models\WoTThingDescription;
use API\StaticClasses\Utils;
use Core\API;
use Core\Controller;
use Core\HttpResponseStatusCodes;

class WoTThingDescriptionController extends Controller
{
    private WorkspaceManager $workspaceManager;
    private PropertyManager $propertyManager;
    private WoTThingDescriptionManager $woTThingDescriptionManager;
    private WoTPropertyManager $woTPropertyManager;
    private WoTActionManager $woTActionManager;
    private WoTEventManager $woTEventManager;

    public function __construct()
    {
        global $systemEntityManager;
        $this->workspaceManager = new WorkspaceManager($systemEntityManager);
        $this->propertyManager = new PropertyManager($systemEntityManager);
        $this->woTThingDescriptionManager = new WoTThingDescriptionManager($systemEntityManager);
        $this->woTPropertyManager = new WoTPropertyManager($systemEntityManager);
        $this->woTActionManager = new WoTActionManager($systemEntityManager);
        $this->woTEventManager = new WoTEventManager($systemEntityManager);
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

    public function build(string $id): void
    {
        $woTThingDescription = $this->woTThingDescriptionManager->readOne($id);

        $workspace = $this->workspaceManager->readOne($woTThingDescription->hasWorkspace);

        $query = "hasWorkspace==\"{$workspace->id}\";hasWoTThingDescription==\"{$woTThingDescription->id}\"";
        $woTProperties = $this->woTPropertyManager->readMultiple($query);

        $query = "hasWorkspace==\"{$workspace->id}\";hasWoTThingDescription==\"{$woTThingDescription->id}\"";
        $woTActions = $this->woTActionManager->readMultiple($query);

        $query = "hasWorkspace==\"{$workspace->id}\";hasWoTThingDescription==\"{$woTThingDescription->id}\"";
        $woTEvents = $this->woTEventManager->readMultiple($query);

        $td = [
            "@context" => "https://www.w3.org/2019/wot/td/v1",
            "title" => $woTThingDescription->name,
            "securityDefinitions" => [
                "nosec_sc" => [
                    "scheme" => "nosec"
                ]
            ],
            "security" => "nosec_sc"
        ];

        if($woTProperties) {
            $td["properties"] = [];

            foreach($woTProperties as $woTProperty) {
                $property = $this->propertyManager->readOne($woTProperty->hasProperty);
                $td["properties"][$property->name] = [
                    "title" => $woTProperty->name,
                    "type" => strtolower($property->propertyNgsiLdValueType),
                    "observable" => false,
                    "readOnly" => false
                ];

                if($property->propertyNgsiLdValueType === NgsiLdPropertyValueType::Number->value) {
                    if($woTProperty->capacityType === "Range") {
                        $capacityValue = json_decode($woTProperty->capacityValue, true);
                        $td["properties"][$property->name]["min"] = $capacityValue[0];
                        $td["properties"][$property->name]["max"] = $capacityValue[1];
                    }
                }
            }
        }

        if($woTActions) {
            $td["actions"] = [];

            foreach($woTActions as $woTAction) {
                $td["actions"][$woTAction->name] = [
                    "title" => $woTAction->description ?? $woTAction->name
                ];
            }
        }

        if($woTEvents) {
            $td["events"] = [];

            foreach($woTEvents as $woTEvent) {
                $td["events"][$woTEvent->name] = [
                    "title" => $woTEvent->description ?? $woTEvent->name
                ];
            }
        }

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($td, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }
}
