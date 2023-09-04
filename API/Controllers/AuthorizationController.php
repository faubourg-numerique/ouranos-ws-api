<?php

namespace API\Controllers;

use API\Enums\MimeType;
use API\StaticClasses\Authorization;
use Core\API;
use Core\Controller;
use Core\HttpResponseStatusCodes;

class AuthorizationController extends Controller
{
    public function permissions(): void
    {
        global $authorizationIdentityManager;

        $permissions = Authorization::getUserPermissions($authorizationIdentityManager);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($permissions, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }
}
