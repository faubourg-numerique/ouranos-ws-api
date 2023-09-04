<?php

namespace API\Controllers;

use API\StaticClasses\Utils;
use API\StaticClasses\Validation;
use API\Enums\MimeType;
use API\Exceptions\ControllerException\ServiceControllerException;
use API\Managers\ServiceManager;
use API\Managers\WorkspaceManager;
use API\Models\Service;
use Core\API;
use Core\Controller;
use Core\HttpResponseStatusCodes;

class ServiceController extends Controller
{
    private ServiceManager $serviceManager;
    private WorkspaceManager $workspaceManager;

    public function __construct()
    {
        global $systemEntityManager;
        $this->serviceManager = new ServiceManager($systemEntityManager);
        $this->workspaceManager = new WorkspaceManager($systemEntityManager);
    }

    public function index(): void
    {
        $services = $this->serviceManager->readMultiple();

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($services, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function store(): void
    {
        $data = API::request()->getDecodedJsonBody();

        Validation::validateService($data);

        $service = new Service($data);
        $service->id = Utils::generateUniqueNgsiLdUrn(Service::TYPE);
        $this->serviceManager->create($service);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_CREATED);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($service, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function show(string $id): void
    {
        $service = $this->serviceManager->readOne($id);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($service, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function update(string $id): void
    {
        $service = $this->serviceManager->readOne($id);

        $data = API::request()->getDecodedJsonBody();

        Validation::validateService($data);

        $service->update($data);

        $this->serviceManager->update($service);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($service, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function destroy(string $id): void
    {
        $service = $this->serviceManager->readOne($id);

        $query = "hasService==\"{$service->id}\"";
        $workspaces = $this->workspaceManager->readMultiple($query);

        if ($workspaces) {
            throw new ServiceControllerException\RelationshipException();
        }

        $this->serviceManager->delete($service);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_NO_CONTENT);
        API::response()->send();
    }
}
