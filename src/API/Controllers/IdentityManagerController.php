<?php

namespace API\Controllers;

use API\StaticClasses\Utils;
use API\StaticClasses\Validation;
use API\Enums\MimeType;
use API\Exceptions\ControllerException\IdentityManagerControllerException;
use API\Managers\IdentityManagerManager;
use API\Managers\ServiceManager;
use API\Managers\TemporalServiceManager;
use API\Models\IdentityManager;
use Core\API;
use Core\Controller;
use Core\HttpResponseStatusCodes;

class IdentityManagerController extends Controller
{
    private IdentityManagerManager $identityManagerManager;
    private ServiceManager $serviceManager;
    private TemporalServiceManager $temporalServiceManager;

    public function __construct()
    {
        global $systemEntityManager;
        $this->identityManagerManager = new IdentityManagerManager($systemEntityManager);
        $this->serviceManager = new ServiceManager($systemEntityManager);
        $this->temporalServiceManager = new TemporalServiceManager($systemEntityManager);
    }

    public function index(): void
    {
        $identityManagers = $this->identityManagerManager->readMultiple();

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($identityManagers, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function store(): void
    {
        $data = API::request()->getDecodedJsonBody();

        Validation::validateIdentityManager($data);

        $identityManager = new IdentityManager($data);
        $identityManager->id = Utils::generateUniqueNgsiLdUrn(IdentityManager::TYPE);
        $this->identityManagerManager->create($identityManager);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_CREATED);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($identityManager, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function show(string $id): void
    {
        $identityManager = $this->identityManagerManager->readOne($id);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($identityManager, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function update(string $id): void
    {
        $identityManager = $this->identityManagerManager->readOne($id);

        $data = API::request()->getDecodedJsonBody();

        Validation::validateIdentityManager($data);

        $identityManager->update($data);

        $this->identityManagerManager->update($identityManager);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($identityManager, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function destroy(string $id): void
    {
        $identityManager = $this->identityManagerManager->readOne($id);

        $query = "hasIdentityManager==\"{$identityManager->id}\"";
        $services = $this->serviceManager->readMultiple($query);

        $query = "hasIdentityManager==\"{$identityManager->id}\"";
        $temporalServices = $this->temporalServiceManager->readMultiple($query);

        if ($services || $temporalServices) {
            throw new IdentityManagerControllerException\RelationshipException();
        }

        $this->identityManagerManager->delete($identityManager);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_NO_CONTENT);
        API::response()->send();
    }
}
