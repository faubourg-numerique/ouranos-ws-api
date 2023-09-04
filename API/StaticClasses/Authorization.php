<?php

namespace API\StaticClasses;

use API\Enums\MimeType;
use API\Exceptions\StaticClassException\AuthorizationException;
use API\Models\IdentityManager;
use API\Models\IdentityManagerGrant;
use Core\API;
use Core\Helpers\RequestHelper;
use Core\HttpRequestMethods;

class Authorization
{
    public static function getAuthorizationBearerTokenFromHeaders(): ?string
    {
        $authorizationHeader = API::request()->getHeader("Authorization");
        if (!$authorizationHeader) return null;
        $authorizationHeader = trim($authorizationHeader);
        $authorizationHeaderParts = explode(" ", $authorizationHeader);
        if (count($authorizationHeaderParts) !== 2) return null;
        list($bearerTokenType, $bearerToken) = $authorizationHeaderParts;
        if ($bearerTokenType !== "Bearer") return null;
        if (!$bearerToken) return null;
        return $bearerToken;
    }

    public static function getAccessToken(IdentityManager $identityManager, IdentityManagerGrant $identityManagerGrant): string
    {
        $authorizationHeader = "Basic " . base64_encode("{$identityManagerGrant->clientId}:{$identityManagerGrant->clientSecret}");

        $query = [
            "grant_type" => $identityManagerGrant->grantType
        ];
        if ($identityManagerGrant->grantType === "password") {
            $query["username"] = $identityManagerGrant->username;
            $query["password"] = $identityManagerGrant->password;
        }

        $request = new RequestHelper();
        $request->setMethod(HttpRequestMethods::POST);
        $request->setUrl($identityManager->getOauth2TokenUrl());
        $request->setHeader("Authorization", $authorizationHeader);
        $request->setHeader("Content-Type", "application/x-www-form-urlencoded");
        $request->setBody(http_build_query($query));
        $request->setTimeout($_ENV["REQUESTS_TIMEOUT"]);
        if ($identityManager->disableCertificateVerification) {
            $request->setDisableCertificateVerification(true);
        }
        $response = $request->send();

        if ($response->getError()) {
            throw new AuthorizationException\AccessTokenRequestException($response);
        }

        $data = $response->getDecodedJsonBody();
        return $data["access_token"];
    }

    public static function getAuthorizationDecision(IdentityManager $identityManager, string $appId, string $accessToken, string $action, string $resource): bool
    {
        $request = new RequestHelper();
        $request->setMethod(HttpRequestMethods::GET);
        $request->setUrl($identityManager->getUserUrl());
        $request->setUrlQueryParameter("access_token", $accessToken);
        $request->setUrlQueryParameter("action", $action);
        $request->setUrlQueryParameter("resource", $resource);
        $request->setUrlQueryParameter("app_id", $appId);
        $request->setTimeout($_ENV["REQUESTS_TIMEOUT"]);
        $response = $request->send();

        if ($response->getError()) {
            throw new AuthorizationException\AuthorizationDecisionRequestException($response);
        }

        $data = $response->getDecodedJsonBody();
        return $data["authorization_decision"] === "Permit";
    }

    public static function getUserPermissions(): array
    {
        $accessToken = self::getAuthorizationBearerTokenFromHeaders();

        $url = Utils::buildUrl(
            $_ENV["AUTHORIZATION_IDENTITY_MANAGER_SCHEME"],
            $_ENV["AUTHORIZATION_IDENTITY_MANAGER_HOST"],
            $_ENV["AUTHORIZATION_IDENTITY_MANAGER_PORT"],
            (isset($_ENV["AUTHORIZATION_IDENTITY_MANAGER_PATH"]) ? $_ENV["AUTHORIZATION_IDENTITY_MANAGER_PATH"] : null) . $_ENV["AUTHORIZATION_IDENTITY_MANAGER_USER_PATH"]
        );

        $request = new RequestHelper();
        $request->setMethod(HttpRequestMethods::GET);
        $request->setUrl($url);
        $request->setUrlQueryParameter("access_token", $accessToken);
        $request->setTimeout($_ENV["REQUESTS_TIMEOUT"]);
        $response = $request->send();

        if ($response->getError()) {
            throw new AuthorizationException\UserRequestException($response);
        }

        $user = $response->getDecodedJsonBody();

        if (!isset($user["roles"])) {
            throw new AuthorizationException\UserRequestException($response);
        }

        $userRoles = array_column($user["roles"], "id");

        $url = Utils::buildUrl(
            $_ENV["AUTHORIZATION_IDENTITY_MANAGER_SCHEME"],
            $_ENV["AUTHORIZATION_IDENTITY_MANAGER_HOST"],
            $_ENV["AUTHORIZATION_IDENTITY_MANAGER_PORT"],
            (isset($_ENV["AUTHORIZATION_IDENTITY_MANAGER_PATH"]) ? $_ENV["AUTHORIZATION_IDENTITY_MANAGER_PATH"] : null) . "/v1/auth/tokens"
        );

        $data = [
            "name" => $_ENV["AUTHORIZATION_IDENTITY_MANAGER_ADMIN_USERNAME"],
            "password" => $_ENV["AUTHORIZATION_IDENTITY_MANAGER_ADMIN_PASSWORD"]
        ];

        $request = new RequestHelper();
        $request->setMethod(HttpRequestMethods::POST);
        $request->setUrl($url);
        $request->setHeader("Accept", MimeType::Json->value);
        $request->setHeader("Content-Type", MimeType::Json->value);
        $request->setJsonBody($data);
        $request->setTimeout($_ENV["REQUESTS_TIMEOUT"]);
        $response = $request->send();

        if ($response->getError()) {
            throw new AuthorizationException\TokensRequestException($response);
        }

        if (!$response->getHeader("X-Subject-Token")) {
            throw new AuthorizationException\TokensRequestException($response);
        }

        $subjectToken = $response->getHeader("X-Subject-Token");

        $permissions = [];
        foreach ($userRoles as $roleId) {
            $url = Utils::buildUrl(
                $_ENV["AUTHORIZATION_IDENTITY_MANAGER_SCHEME"],
                $_ENV["AUTHORIZATION_IDENTITY_MANAGER_HOST"],
                $_ENV["AUTHORIZATION_IDENTITY_MANAGER_PORT"],
                (isset($_ENV["AUTHORIZATION_IDENTITY_MANAGER_PATH"]) ? $_ENV["AUTHORIZATION_IDENTITY_MANAGER_PATH"] : null) . "/v1/applications/{$_ENV["AUTHORIZATION_IDENTITY_MANAGER_APP_ID"]}/roles/{$roleId}/permissions"
            );

            $request = new RequestHelper();
            $request->setMethod(HttpRequestMethods::GET);
            $request->setUrl($url);
            $request->setHeader("X-Auth-Token", $subjectToken);
            $request->setTimeout($_ENV["REQUESTS_TIMEOUT"]);
            $response = $request->send();

            if ($response->getError()) {
                continue;
            }

            $data = $response->getDecodedJsonBody();

            if (!isset($data["role_permission_assignments"])) {
                continue;
            }

            foreach ($data["role_permission_assignments"] as $permission) {
                if (!isset($permission["action"], $permission["resource"])) {
                    continue;
                }
                $permissions[] = [
                    "action" => $permission["action"],
                    "resource" => $permission["resource"]
                ];
            }
        }

        return $permissions;
    }
}
