<?php

use API\StaticClasses\ExceptionHandler;
use API\Models\IdentityManager;
use Core\API;
use Core\HttpResponseStatusCodes;
use Defuse\Crypto\Key;

$dotenv->required("ACCESS_CONTROL_ALLOW_ORIGIN_HEADER")->notEmpty();
$dotenv->required("REQUESTS_TIMEOUT")->isInteger();
$dotenv->required("ENCRYPTION_KEY")->notEmpty();

$dotenv->required("DATA_MODELS_SCHEME")->allowedValues(["http", "https"]);
$dotenv->required("DATA_MODELS_HOST")->notEmpty();
$dotenv->required("DATA_MODELS_PORT")->isInteger();
$dotenv->required("DATA_MODELS_DIRECTORY_PATH")->notEmpty();

API::response()->setHeader("Access-Control-Allow-Origin", $_ENV["ACCESS_CONTROL_ALLOW_ORIGIN_HEADER"]);

if (API::request()->getMethod() === "OPTIONS") {
    API::response()->setHeader("Access-Control-Allow-Headers", "Authorization, Accept, Content-Type");
    API::response()->setHeader("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE");
    API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
    API::response()->send();
}

$encryptionKey = Key::loadFromAsciiSafeString($_ENV["ENCRYPTION_KEY"]);

$authorizationIdentityManager = new IdentityManager();
$authorizationIdentityManager->scheme = $_ENV["AUTHORIZATION_IDENTITY_MANAGER_SCHEME"];
$authorizationIdentityManager->host = $_ENV["AUTHORIZATION_IDENTITY_MANAGER_HOST"];
$authorizationIdentityManager->port = $_ENV["AUTHORIZATION_IDENTITY_MANAGER_PORT"];
if (isset($_ENV["AUTHORIZATION_IDENTITY_MANAGER_PATH"])) {
    $authorizationIdentityManager->path = $_ENV["AUTHORIZATION_IDENTITY_MANAGER_PATH"];
}
$authorizationIdentityManager->userPath = $_ENV["AUTHORIZATION_IDENTITY_MANAGER_USER_PATH"];

set_exception_handler([ExceptionHandler::class, "handler"]);
