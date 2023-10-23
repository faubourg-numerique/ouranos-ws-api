<?php

namespace API\Controllers;

use API\StaticClasses\Utils;
use API\Enums\MimeType;
use API\Managers\ContextBrokerManager;
use API\Managers\EntityManager;
use API\Managers\IdentityManagerGrantManager;
use API\Managers\IdentityManagerManager;
use API\Managers\ServiceManager;
use API\Managers\WorkspaceManager;
use API\Models\Entity;
use API\Models\Workspace;
use API\StaticClasses\Validation;
use Core\API;
use Core\Controller;
use Core\HttpResponseStatusCodes;

class EntityController extends Controller
{
    private WorkspaceManager $workspaceManager;

    public function __construct()
    {
        global $systemEntityManager;
        $this->workspaceManager = new WorkspaceManager($systemEntityManager);
    }

    public function index(string $workspaceId): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);
        $entityManager = $this->buildEntityManager($workspace);

        $id = API::request()->getUrlQueryParameter("id");
        $type = API::request()->getUrlQueryParameter("type");
        $query = API::request()->getUrlQueryParameter("q");
        $limit = API::request()->getUrlQueryParameter("limit");
        $offset = API::request()->getUrlQueryParameter("offset");

        $entities = $entityManager->readMultiple($id, $type, $query, $limit, $offset);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($entities, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function store(string $workspaceId): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);
        $entityManager = $this->buildEntityManager($workspace);

        $data = API::request()->getDecodedJsonBody();

        Validation::validateEntityCreation($data);

        $entity = new Entity($data);

        if (!isset($entity->id)) {
            $entity->id = Utils::generateUniqueNgsiLdUrn($entity->type);
        }

        $entityManager->create($entity);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_CREATED);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($entity, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function show(string $workspaceId, string $id): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);
        $entityManager = $this->buildEntityManager($workspace);

        $entity = $entityManager->readOne($id);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($entity, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function update(string $workspaceId, string $id): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);
        $entityManager = $this->buildEntityManager($workspace);

        $data = API::request()->getDecodedJsonBody();

        Validation::validateEntityUpdate($data);

        $entity = $entityManager->readOne($id);

        foreach ($entity as $name => $value) {
            if (in_array($name, ["id", "type"], true)) continue;
            unset($entity->$name);
        }

        foreach ($data as $name => $value) {
            $entity->$name = $value;
        }

        $entityManager->update($entity);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($entity, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function destroy(string $workspaceId, string $id): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);
        $entityManager = $this->buildEntityManager($workspace);

        $entity = $entityManager->readOne($id);

        $entityManager->delete($entity);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_NO_CONTENT);
        API::response()->send();
    }

    public function buildEntityManager(Workspace $workspace): EntityManager
    {
        global $systemEntityManager;

        $serviceManager = new ServiceManager($systemEntityManager);
        $contextBrokerManager = new ContextBrokerManager($systemEntityManager);
        $identityManagerManager = new IdentityManagerManager($systemEntityManager);
        $identityManagerGrantManager = new IdentityManagerGrantManager($systemEntityManager);

        $service = $serviceManager->readOne($workspace->hasService);
        $contextBroker = $contextBrokerManager->readOne($service->hasContextBroker);

        $identityManager = null;
        $identityManagerGrant = null;

        if ($service->authorizationRequired && (!$service->authorizationMode || $service->authorizationMode == "oauth2")) {
            $identityManager = $identityManagerManager->readOne($service->hasIdentityManager);
            $identityManagerGrant = $identityManagerGrantManager->readOne($service->hasIdentityManagerGrant);
        }

        return new EntityManager($workspace, $service, $contextBroker, $identityManager, $identityManagerGrant);
    }
}
