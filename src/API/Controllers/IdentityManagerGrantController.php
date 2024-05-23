<?php

namespace API\Controllers;

use API\StaticClasses\Utils;
use API\StaticClasses\Validation;
use API\Enums\MimeType;
use API\Exceptions\ControllerException\IdentityManagerGrantControllerException;
use API\Managers\IdentityManagerGrantManager;
use API\Managers\ServiceManager;
use API\Managers\TemporalServiceManager;
use API\Models\IdentityManagerGrant;
use Core\API;
use Core\Controller;
use Core\HttpResponseStatusCodes;

class IdentityManagerGrantController extends Controller
{
    private IdentityManagerGrantManager $identityManagerGrantManager;
    private ServiceManager $serviceManager;
    private TemporalServiceManager $temporalServiceManager;

    public function __construct()
    {
        global $systemEntityManager;
        $this->identityManagerGrantManager = new IdentityManagerGrantManager($systemEntityManager);
        $this->serviceManager = new ServiceManager($systemEntityManager);
        $this->temporalServiceManager = new TemporalServiceManager($systemEntityManager);
    }

    public function index(): void
    {
        $identityManagerGrants = $this->identityManagerGrantManager->readMultiple();

        foreach (array_keys($identityManagerGrants) as $index) {
            $identityManagerGrants[$index]->clientId = null;
            $identityManagerGrants[$index]->clientSecret = null;
            $identityManagerGrants[$index]->username = null;
            $identityManagerGrants[$index]->password = null;
        }

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($identityManagerGrants, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function store(): void
    {
        $data = API::request()->getDecodedJsonBody();

        Validation::validateIdentityManagerGrant($data);

        $identityManagerGrant = new IdentityManagerGrant($data);
        $identityManagerGrant->id = Utils::generateUniqueNgsiLdUrn(IdentityManagerGrant::TYPE);
        $this->identityManagerGrantManager->create($identityManagerGrant);

        $identityManagerGrant->clientId = null;
        $identityManagerGrant->clientSecret = null;
        $identityManagerGrant->username = null;
        $identityManagerGrant->password = null;

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_CREATED);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($identityManagerGrant, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function show(string $id): void
    {
        $identityManagerGrant = $this->identityManagerGrantManager->readOne($id);

        $identityManagerGrant->clientId = null;
        $identityManagerGrant->clientSecret = null;
        $identityManagerGrant->username = null;
        $identityManagerGrant->password = null;

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($identityManagerGrant, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function update(string $id): void
    {
        $identityManagerGrant = $this->identityManagerGrantManager->readOne($id);

        $data = API::request()->getDecodedJsonBody();

        Validation::validateIdentityManagerGrant($data);

        $identityManagerGrant->update($data);

        $this->identityManagerGrantManager->update($identityManagerGrant);

        $identityManagerGrant->clientId = null;
        $identityManagerGrant->clientSecret = null;
        $identityManagerGrant->username = null;
        $identityManagerGrant->password = null;

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($identityManagerGrant, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function destroy(string $id): void
    {
        $identityManagerGrant = $this->identityManagerGrantManager->readOne($id);

        $query = "hasIdentityManagerGrant==\"{$identityManagerGrant->id}\"";
        $services = $this->serviceManager->readMultiple($query);

        $query = "hasIdentityManagerGrant==\"{$identityManagerGrant->id}\"";
        $temporalServices = $this->temporalServiceManager->readMultiple($query);

        if ($services || $temporalServices) {
            throw new IdentityManagerGrantControllerException\RelationshipException();
        }

        $this->identityManagerGrantManager->delete($identityManagerGrant);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_NO_CONTENT);
        API::response()->send();
    }
}
