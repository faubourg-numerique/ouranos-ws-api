<?php

namespace API\Controllers;

use API\StaticClasses\Utils;
use API\StaticClasses\Validation;
use API\Enums\MimeType;
use API\Exceptions\ControllerException\TemporalServiceControllerException;
use API\Managers\PropertyManager;
use API\Managers\TemporalServiceManager;
use API\Managers\WorkspaceManager;
use API\Models\TemporalService;
use Core\API;
use Core\Controller;
use Core\HttpResponseStatusCodes;

class TemporalServiceController extends Controller
{
    private TemporalServiceManager $temporalServiceManager;
    private PropertyManager $propertyManager;
    private WorkspaceManager $workspaceManager;

    public function __construct()
    {
        global $systemEntityManager;
        $this->temporalServiceManager = new TemporalServiceManager($systemEntityManager);
        $this->propertyManager = new PropertyManager($systemEntityManager);
        $this->workspaceManager = new WorkspaceManager($systemEntityManager);
    }

    public function index(): void
    {
        $temporalServices = $this->temporalServiceManager->readMultiple();

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($temporalServices, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function store(): void
    {
        $data = API::request()->getDecodedJsonBody();

        Validation::validateTemporalServiceType($data);

        $temporalService = null;
        if ($data["temporalServiceType"] === "ngsi-ld") {
            Validation::validateTemporalServiceNgsiLd($data);
            $temporalService = new TemporalService\NgsiLd($data);
        } else if ($data["temporalServiceType"] === "mintaka") {
            Validation::validateTemporalServiceMintaka($data);
            $temporalService = new TemporalService\Mintaka($data);
        }

        $temporalService->id = Utils::generateUniqueNgsiLdUrn(TemporalService::TYPE);
        $this->temporalServiceManager->create($temporalService);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_CREATED);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($temporalService, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function show(string $id): void
    {
        $temporalService = $this->temporalServiceManager->readOne($id);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($temporalService, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function update(string $id): void
    {
        $temporalService = $this->temporalServiceManager->readOne($id);

        $data = API::request()->getDecodedJsonBody();

        Validation::validateTemporalServiceType($data);

        if ($data["temporalServiceType"] === "ngsi-ld") {
            Validation::validateTemporalServiceNgsiLd($data);
        } else if ($data["temporalServiceType"] === "mintaka") {
            Validation::validateTemporalServiceMintaka($data);
        }

        $temporalService->update($data);

        $this->temporalServiceManager->update($temporalService);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($temporalService, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function destroy(string $id): void
    {
        $temporalService = $this->temporalServiceManager->readOne($id);

        $properties = $this->propertyManager->readMultiple();
        $workspaces = $this->workspaceManager->readMultiple();

        foreach ($properties as $property) {
            if (!$property->temporal) continue;
            if (!in_array($temporalService->id, $property->temporalServices, true)) continue;
            throw new TemporalServiceControllerException\RelationshipException();
        }

        foreach ($workspaces as $workspace) {
            if (!in_array($temporalService->id, $workspace->temporalServices, true)) continue;
            throw new TemporalServiceControllerException\RelationshipException();
        }

        $this->temporalServiceManager->delete($temporalService);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_NO_CONTENT);
        API::response()->send();
    }
}
