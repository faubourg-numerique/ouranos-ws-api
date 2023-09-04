<?php

namespace API\Middlewares;

use API\StaticClasses\Authorization;
use Core\API;
use Core\HttpResponseStatusCodes;

final class AuthorizationMiddleware
{
    public function authorize(): void
    {
        global $authorizationIdentityManager;

        if (str_ends_with(API::request()->getPath(), "/authorization-registry-bridge/create-policy")) {
            return;
        }

        $accessToken = Authorization::getAuthorizationBearerTokenFromHeaders();

        if (!$accessToken) {
            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_UNAUTHORIZED);
            API::response()->send();
        }

        $appId = $_ENV["AUTHORIZATION_IDENTITY_MANAGER_APP_ID"];
        $action = API::request()->getMethod();
        $resource = API::request()->getPath();

        $authorized = Authorization::getAuthorizationDecision($authorizationIdentityManager, $appId, $accessToken, $action, $resource);

        if (!$authorized) {
            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_FORBIDDEN);
            API::response()->send();
        }
    }
}
