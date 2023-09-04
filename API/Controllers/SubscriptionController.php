<?php

namespace API\Controllers;

use API\StaticClasses\Utils;
use API\Enums\MimeType;
use API\Managers\ContextBrokerManager;
use API\Managers\IdentityManagerGrantManager;
use API\Managers\IdentityManagerManager;
use API\Managers\ServiceManager;
use API\Managers\SubscriptionManager;
use API\Managers\WorkspaceManager;
use API\Models\Subscription;
use API\Models\Workspace;
use API\StaticClasses\Validation;
use Core\API;
use Core\Controller;
use Core\HttpResponseStatusCodes;

class SubscriptionController extends Controller
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
        $subscriptionManager = $this->buildSubscriptionManager($workspace);

        $elements = $subscriptionManager->readMultiple();

        $type = API::request()->getUrlQueryParameter("type");

        $subscriptions = [];
        foreach ($elements as $element) {
            if (!isset($element->entities)) continue;
            if (!$element->entities) continue;
            foreach ($element->entities as $entity) {
                if (!isset($entity["type"])) continue;
                if ($entity["type"] !== $type) continue;
                $subscriptions[] = $element;
                break;
            }
        }

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($subscriptions, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function store(string $workspaceId): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);
        $subscriptionManager = $this->buildSubscriptionManager($workspace);

        $data = API::request()->getDecodedJsonBody();

        Validation::validateSubscriptionCreation($data);

        $subscription = new Subscription($data);

        if (!isset($subscription->id)) {
            $subscription->id = Utils::generateUniqueNgsiLdUrn(Subscription::TYPE);
        }

        $subscriptionManager->create($subscription);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_CREATED);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($subscription, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function show(string $workspaceId, string $id): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);
        $subscriptionManager = $this->buildSubscriptionManager($workspace);

        $subscription = $subscriptionManager->readOne($id);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($subscription, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function destroy(string $workspaceId, string $id): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);
        $subscriptionManager = $this->buildSubscriptionManager($workspace);

        $subscription = $subscriptionManager->readOne($id);

        $subscriptionManager->delete($subscription);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_NO_CONTENT);
        API::response()->send();
    }

    private function buildSubscriptionManager(Workspace $workspace): SubscriptionManager
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

        if ($service->authorizationRequired) {
            $identityManager = $identityManagerManager->readOne($service->hasIdentityManager);
            $identityManagerGrant = $identityManagerGrantManager->readOne($service->hasIdentityManagerGrant);
        }

        return new SubscriptionManager($workspace, $service, $contextBroker, $identityManager, $identityManagerGrant);
    }
}
