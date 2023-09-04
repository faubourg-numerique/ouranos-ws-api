<?php

namespace API\Controllers;

use API\StaticClasses\Utils;
use API\StaticClasses\Validation;
use API\Enums\MimeType;
use API\Exceptions\ControllerException\ContextBrokerControllerException;
use API\Managers\ContextBrokerManager;
use API\Managers\ServiceManager;
use API\Models\ContextBroker;
use Core\API;
use Core\Controller;
use Core\HttpResponseStatusCodes;

class ContextBrokerController extends Controller
{
    private ContextBrokerManager $contextBrokerManager;
    private ServiceManager $serviceManager;

    public function __construct()
    {
        global $systemEntityManager;
        $this->contextBrokerManager = new ContextBrokerManager($systemEntityManager);
        $this->serviceManager = new ServiceManager($systemEntityManager);
    }

    public function index(): void
    {
        $contextBrokers = $this->contextBrokerManager->readMultiple();

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($contextBrokers, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function store(): void
    {
        $data = API::request()->getDecodedJsonBody();

        Validation::validateContextBroker($data);

        $contextBroker = new ContextBroker($data);
        $contextBroker->id = Utils::generateUniqueNgsiLdUrn(ContextBroker::TYPE);
        $this->contextBrokerManager->create($contextBroker);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_CREATED);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($contextBroker, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function show(string $id): void
    {
        $contextBroker = $this->contextBrokerManager->readOne($id);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($contextBroker, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function update(string $id): void
    {
        $contextBroker = $this->contextBrokerManager->readOne($id);

        $data = API::request()->getDecodedJsonBody();

        Validation::validateContextBroker($data);

        $contextBroker->update($data);

        $this->contextBrokerManager->update($contextBroker);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($contextBroker, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function destroy(string $id): void
    {
        $contextBroker = $this->contextBrokerManager->readOne($id);

        $query = "hasContextBroker==\"{$contextBroker->id}\"";
        $services = $this->serviceManager->readMultiple($query);

        if ($services) {
            throw new ContextBrokerControllerException\RelationshipException();
        }

        $this->contextBrokerManager->delete($contextBroker);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_NO_CONTENT);
        API::response()->send();
    }
}
