<?php

namespace API\Controllers;

use API\Controllers\EntityController;
use API\Enums\MimeType;
use API\Models\ErrorInfo;
use API\Managers\TypeManager;
use API\Managers\WorkspaceManager;
use API\Managers\IdentityManagerManager;
use API\Managers\IdentityManagerGrantManager;
use API\Modules\DataServices\Managers\DataServiceManager;
use API\Modules\DataServices\Managers\DataServiceAccessManager;
use API\Modules\DataServices\Managers\DataServiceActionManager;
use API\Modules\DataServices\Managers\DataActionManager;
use API\Modules\DSC\Managers\ContractDetailManager;
use API\Modules\DSC\Managers\ContractManager;
use API\Modules\DSC\Managers\RoleManager;
use API\Modules\DSC\Models\ContractDetail;
use API\StaticClasses\Validation;
use API\StaticClasses\Utils;
use Core\Helpers\RequestHelper;
use Core\API;
use Core\Controller;
use Core\HttpRequestMethods;
use Core\HttpResponseStatusCodes;

class UserController extends Controller
{
    private TypeManager $typeManager;
    private WorkspaceManager $workspaceManager;
    private IdentityManagerManager $identityManagerManager;
    private IdentityManagerGrantManager $identityManagerGrantManager;
    private DataServiceManager $dataServiceManager;
    private DataServiceAccessManager $dataServiceAccessManager;
    private DataServiceActionManager $dataServiceActionManager;
    private DataActionManager $dataActionManager;
    private ContractDetailManager $contractDetailManager;
    private ContractManager $contractManager;
    private RoleManager $roleManager;

    public function __construct()
    {
        global $systemEntityManager;
        $this->typeManager = new TypeManager($systemEntityManager);
        $this->workspaceManager = new WorkspaceManager($systemEntityManager);
        $this->identityManagerManager = new IdentityManagerManager($systemEntityManager);
        $this->identityManagerGrantManager = new IdentityManagerGrantManager($systemEntityManager);
        $this->dataServiceManager = new DataServiceManager($systemEntityManager);
        $this->dataServiceAccessManager = new DataServiceAccessManager($systemEntityManager);
        $this->dataServiceActionManager = new DataServiceActionManager($systemEntityManager);
        $this->dataActionManager = new DataActionManager($systemEntityManager);
        $this->contractDetailManager = new ContractDetailManager($systemEntityManager);
        $this->contractManager = new ContractManager($systemEntityManager);
        $this->roleManager = new RoleManager($systemEntityManager);
    }

    public function index(string $workspaceId): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        if (!$workspace->hasIdentityManager || !$workspace->hasIdentityManagerGrant) {
            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_BAD_REQUEST);
            API::response()->send();
        }

        $identityManager = $this->identityManagerManager->readOne($workspace->hasIdentityManager);
        $identityManagerGrant = $this->identityManagerGrantManager->readOne($workspace->hasIdentityManagerGrant);

        if ($identityManagerGrant->grantType !== "password") {
            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_BAD_REQUEST);
            API::response()->send();
        }

        $idm = new \GuzzleHttp\Client([
            "base_uri" => $identityManager->getUrl(),
            "timeout"  => $_ENV["REQUESTS_TIMEOUT"]
        ]);

        $response = $idm->post("v1/auth/tokens", [
            "json" => [
                "name" => $identityManagerGrant->username,
                "password" => $identityManagerGrant->password
            ]
        ]);

        if (!$response->hasHeader("X-Subject-Token")) {
            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_UNAUTHORIZED);
            API::response()->send();
        }

        $accessToken = $response->getHeader("X-Subject-Token");

        $idm = new \GuzzleHttp\Client([
            "base_uri" => $identityManager->getUrl(),
            "timeout"  => $_ENV["REQUESTS_TIMEOUT"],
            "headers" => [
                "X-Auth-Token" => $accessToken
            ]
        ]);

        $response = $idm->get("v1/users");
        $data = json_decode($response->getBody(), true);
        $users = $data["users"];

        $response = $idm->get("v1/applications/{$identityManagerGrant->clientId}/roles");
        $data = json_decode($response->getBody(), true);

        $roles = [];
        foreach ($data["roles"] as $role) {
            $roles[$role["id"]] = $role["name"];
        }

        foreach ($users as &$user) {
            $user["roles"] = [];
            $response = $idm->get("v1/applications/{$identityManagerGrant->clientId}/users/{$user["id"]}/roles");
            $data = json_decode($response->getBody(), true);
            $roleUserAssignments = $data["role_user_assignments"];
            foreach ($roleUserAssignments as $roleUserAssignment) {
                if (array_key_exists($roleUserAssignment["role_id"], $roles)) {
                    $user["roles"][] = $roles[$roleUserAssignment["role_id"]];
                }
            }
        }

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($users, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function assignRole(string $workspaceId, string $userId, string $roleName): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        if (!$workspace->hasIdentityManager || !$workspace->hasIdentityManagerGrant) {
            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_BAD_REQUEST);
            API::response()->send();
        }

        $identityManager = $this->identityManagerManager->readOne($workspace->hasIdentityManager);
        $identityManagerGrant = $this->identityManagerGrantManager->readOne($workspace->hasIdentityManagerGrant);

        if ($identityManagerGrant->grantType !== "password") {
            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_BAD_REQUEST);
            API::response()->send();
        }

        $idm = new \GuzzleHttp\Client([
            "base_uri" => $identityManager->getUrl(),
            "timeout"  => $_ENV["REQUESTS_TIMEOUT"]
        ]);

        $response = $idm->post("v1/auth/tokens", [
            "json" => [
                "name" => $identityManagerGrant->username,
                "password" => $identityManagerGrant->password
            ]
        ]);

        if (!$response->hasHeader("X-Subject-Token")) {
            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_UNAUTHORIZED);
            API::response()->send();
        }

        $accessToken = $response->getHeader("X-Subject-Token");

        $idm = new \GuzzleHttp\Client([
            "base_uri" => $identityManager->getUrl(),
            "timeout"  => $_ENV["REQUESTS_TIMEOUT"],
            "headers" => [
                "X-Auth-Token" => $accessToken
            ]
        ]);

        $response = $idm->get("v1/applications/{$identityManagerGrant->clientId}/roles");
        $data = json_decode($response->getBody(), true);
        $roles = $data["roles"];

        $roleId = null;
        foreach ($roles as $role) {
            if ($role["name"] === $roleName) {
                $roleId = $role["id"];
                break;
            }
        }

        if (!$roleId) {
            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_NOT_FOUND);
            API::response()->send();
        }

        $idm->post("v1/applications/{$identityManagerGrant->clientId}/users/{$userId}/roles/{$roleId}", [
            "headers" => [
                "Content-Type" => MimeType::Json->value
            ]
        ]);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_NO_CONTENT);
        API::response()->send();
    }

    public function removeRole(string $workspaceId, string $userId, string $roleName): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        if (!$workspace->hasIdentityManager || !$workspace->hasIdentityManagerGrant) {
            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_BAD_REQUEST);
            API::response()->send();
        }

        $identityManager = $this->identityManagerManager->readOne($workspace->hasIdentityManager);
        $identityManagerGrant = $this->identityManagerGrantManager->readOne($workspace->hasIdentityManagerGrant);

        if ($identityManagerGrant->grantType !== "password") {
            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_BAD_REQUEST);
            API::response()->send();
        }

        $idm = new \GuzzleHttp\Client([
            "base_uri" => $identityManager->getUrl(),
            "timeout"  => $_ENV["REQUESTS_TIMEOUT"]
        ]);

        $response = $idm->post("v1/auth/tokens", [
            "json" => [
                "name" => $identityManagerGrant->username,
                "password" => $identityManagerGrant->password
            ]
        ]);

        if (!$response->hasHeader("X-Subject-Token")) {
            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_UNAUTHORIZED);
            API::response()->send();
        }

        $accessToken = $response->getHeader("X-Subject-Token");

        $idm = new \GuzzleHttp\Client([
            "base_uri" => $identityManager->getUrl(),
            "timeout"  => $_ENV["REQUESTS_TIMEOUT"],
            "headers" => [
                "X-Auth-Token" => $accessToken
            ]
        ]);

        $response = $idm->get("v1/applications/{$identityManagerGrant->clientId}/roles");
        $data = json_decode($response->getBody(), true);
        $roles = $data["roles"];

        $roleId = null;
        foreach ($roles as $role) {
            if ($role["name"] === $roleName) {
                $roleId = $role["id"];
                break;
            }
        }

        if (!$roleId) {
            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_NOT_FOUND);
            API::response()->send();
        }

        $idm->delete("v1/applications/{$identityManagerGrant->clientId}/users/{$userId}/roles/{$roleId}");

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_NO_CONTENT);
        API::response()->send();
    }
}
