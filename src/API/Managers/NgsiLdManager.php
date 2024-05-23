<?php

namespace API\Managers;

use API\StaticClasses\Authorization;
use API\Models\ContextBroker;
use API\Models\IdentityManager;
use API\Models\IdentityManagerGrant;
use API\Models\Service;
use API\Models\Workspace;
use Core\API;
use Core\Manager;

abstract class NgsiLdManager extends Manager
{
    protected ContextBroker $contextBroker;
    protected Service $service;
    protected Workspace $workspace;
    protected ?string $authorizationHeader = null;

    public function __construct(Workspace $workspace, Service $service, ContextBroker $contextBroker, ?IdentityManager $identityManager = null, ?IdentityManagerGrant $identityManagerGrant = null)
    {
        $this->contextBroker = $contextBroker;
        $this->service = $service;
        $this->workspace = $workspace;

        if ($service->authorizationRequired) {
            if ($service->authorizationMode && $service->authorizationMode == "siop2") {
                $this->authorizationHeader = API::request()->getHeader("Gateway-Authorization");
            } else {
                $accessToken = Authorization::getAccessToken($identityManager, $identityManagerGrant);
                $this->authorizationHeader = "Bearer {$accessToken}";
            }
        }
    }
}
