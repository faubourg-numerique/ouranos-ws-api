<?php

namespace API\Controllers;

use API\Enums\MimeType;
use API\Enums\TemporalService;
use API\Managers\ContextBrokerManager;
use API\Managers\IdentityManagerGrantManager;
use API\Managers\IdentityManagerManager;
use API\Managers\PropertyManager;
use API\Managers\ServiceManager;
use API\Managers\TemporalEntityPropertyManager;
use API\Managers\TemporalServiceManager;
use API\Managers\WorkspaceManager;
use API\Models\TemporalEntityProperty;
use Core\API;
use Core\Controller;
use Core\HttpResponseStatusCodes;

class TemporalEntityPropertyController extends Controller
{
    private WorkspaceManager $workspaceManager;

    public function __construct()
    {
        global $systemEntityManager;
        $this->workspaceManager = new WorkspaceManager($systemEntityManager);
    }

    public function show(string $workspaceId, string $entityId, string $propertyId, string $temporalServiceId): void
    {
        global $systemEntityManager;

        $workspace = $this->workspaceManager->readOne($workspaceId);

        $serviceManager = new ServiceManager($systemEntityManager);
        $contextBrokerManager = new ContextBrokerManager($systemEntityManager);
        $identityManagerManager = new IdentityManagerManager($systemEntityManager);
        $identityManagerGrantManager = new IdentityManagerGrantManager($systemEntityManager);
        $temporalServiceManager = new TemporalServiceManager($systemEntityManager);
        $propertyManager = new PropertyManager($systemEntityManager);

        $service = $serviceManager->readOne($workspace->hasService);
        $contextBroker = $contextBrokerManager->readOne($service->hasContextBroker);

        $identityManager = null;
        $identityManagerGrant = null;

        if ($service->authorizationRequired) {
            $identityManager = $identityManagerManager->readOne($service->hasIdentityManager);
            $identityManagerGrant = $identityManagerGrantManager->readOne($service->hasIdentityManagerGrant);
        }

        $temporalService = $temporalServiceManager->readOne($temporalServiceId);

        $temporalServiceIdentityManager = null;
        $temporalServiceIdentityManagerGrant = null;

        if ($temporalService->temporalServiceType === TemporalService::Mintaka->value && $temporalService->authorizationRequired && (!$temporalService->authorizationMode || $temporalService->authorizationMode == "oauth2")) {
            $temporalServiceIdentityManager = $identityManagerManager->readOne($temporalService->hasIdentityManager);
            $temporalServiceIdentityManagerGrant = $identityManagerGrantManager->readOne($temporalService->hasIdentityManagerGrant);
        }

        $property = $propertyManager->readOne($propertyId);

        $temporalEntityPropertyManager = new TemporalEntityPropertyManager($temporalService, $workspace, $service, $contextBroker, $identityManager, $identityManagerGrant, $temporalServiceIdentityManager, $temporalServiceIdentityManagerGrant);

        $temporalEntityProperty = new TemporalEntityProperty();
        $temporalEntityProperty->id = $entityId;
        $temporalEntityProperty->name = $property->name;

        if (API::request()->getUrlQueryParameter("fromTime")) {
            $temporalEntityProperty->fromTime = intval(API::request()->getUrlQueryParameter("fromTime"));
        }
        if (API::request()->getUrlQueryParameter("toTime")) {
            $temporalEntityProperty->toTime = intval(API::request()->getUrlQueryParameter("toTime"));
        }

        $temporalEntityProperty = $temporalEntityPropertyManager->read($temporalEntityProperty);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($temporalEntityProperty, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }
}
