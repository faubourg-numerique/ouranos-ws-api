<?php

use API\StaticClasses\Utils;
use API\Managers\EntityManager;
use API\Models\ContextBroker;
use API\Models\IdentityManager;
use API\Models\IdentityManagerGrant;
use API\Models\Service;
use API\Models\Workspace;

$systemWorkspace = new Workspace();
$systemWorkspace->dataModelName = $_ENV["SYSTEM_WORKSPACE_DATA_MODEL_NAME"];
$systemWorkspace->dataModelVersion = $_ENV["SYSTEM_WORKSPACE_DATA_MODEL_VERSION"];
if (isset($_ENV["SYSTEM_WORKSPACE_CONTEXT_BROKER_TENANT"])) {
    $systemWorkspace->contextBrokerTenant = $_ENV["SYSTEM_WORKSPACE_CONTEXT_BROKER_TENANT"];
}

$systemService = new Service();
$systemService->authorizationRequired = Utils::convertToBoolean($_ENV["SYSTEM_SERVICE_AUTHORIZATION_REQUIRED"]);

$systemContextBroker = new ContextBroker();
$systemContextBroker->scheme = $_ENV["SYSTEM_CONTEXT_BROKER_SCHEME"];
$systemContextBroker->host = $_ENV["SYSTEM_CONTEXT_BROKER_HOST"];
$systemContextBroker->port = $_ENV["SYSTEM_CONTEXT_BROKER_PORT"];
if (isset($_ENV["SYSTEM_CONTEXT_BROKER_PATH"])) {
    $systemContextBroker->path = $_ENV["SYSTEM_CONTEXT_BROKER_PATH"];
}
$systemContextBroker->multiTenancyEnabled = Utils::convertToBoolean($_ENV["SYSTEM_CONTEXT_BROKER_MULTI_TENANCY_ENABLED"]);
$systemContextBroker->paginationMaxLimit = $_ENV["SYSTEM_CONTEXT_BROKER_PAGINATION_MAX_LIMIT"];
$systemContextBroker->implementationName = $_ENV["SYSTEM_CONTEXT_BROKER_IMPLEMENTATION_NAME"];
$systemContextBroker->implementationVersion = $_ENV["SYSTEM_CONTEXT_BROKER_IMPLEMENTATION_VERSION"];

if ($systemService->authorizationRequired) {
    $systemIdentityManager = new IdentityManager();
    $systemIdentityManager->scheme = $_ENV["SYSTEM_IDENTITY_MANAGER_SCHEME"];
    $systemIdentityManager->host = $_ENV["SYSTEM_IDENTITY_MANAGER_HOST"];
    $systemIdentityManager->port = $_ENV["SYSTEM_IDENTITY_MANAGER_PORT"];
    if ($_ENV["SYSTEM_IDENTITY_MANAGER_PATH"]) {
        $systemIdentityManager->path = $_ENV["SYSTEM_IDENTITY_MANAGER_PATH"];
    }
    $systemIdentityManager->oauth2TokenPath = $_ENV["SYSTEM_IDENTITY_MANAGER_OAUTH2_TOKEN_PATH"];
    $systemIdentityManager->implementationName = $_ENV["SYSTEM_IDENTITY_MANAGER_IMPLEMENTATION_NAME"];
    $systemIdentityManager->implementationVersion = $_ENV["SYSTEM_IDENTITY_MANAGER_IMPLEMENTATION_VERSION"];

    $systemIdentityManagerGrant = new IdentityManagerGrant();
    $systemIdentityManagerGrant->grantType = $_ENV["SYSTEM_IDENTITY_MANAGER_GRANT_GRANT_TYPE"];
    $systemIdentityManagerGrant->clientId = $_ENV["SYSTEM_IDENTITY_MANAGER_GRANT_CLIENT_ID"];
    $systemIdentityManagerGrant->clientSecret = $_ENV["SYSTEM_IDENTITY_MANAGER_GRANT_CLIENT_SECRET"];
    if ($systemIdentityManagerGrant->grantType === "password") {
        $systemIdentityManagerGrant->username = $_ENV["SYSTEM_IDENTITY_MANAGER_GRANT_USERNAME"];
        $systemIdentityManagerGrant->password = $_ENV["SYSTEM_IDENTITY_MANAGER_GRANT_PASSWORD"];
    }

    $systemEntityManager = new EntityManager($systemWorkspace, $systemService, $systemContextBroker, $systemIdentityManager, $systemIdentityManagerGrant);
} else {
    $systemEntityManager = new EntityManager($systemWorkspace, $systemService, $systemContextBroker);
}
