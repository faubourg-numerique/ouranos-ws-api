<?php

namespace API\Managers;

use API\Enums\ContextBrokerImplementationName;
use API\StaticClasses\Utils;
use API\Enums\MimeType;
use API\Enums\TemporalService;
use API\Exceptions\ManagerException\EntityManagerException;
use API\Models\Entity;
use Core\Helpers\RequestHelper;
use Core\HttpRequestMethods;
use Core\HttpResponseStatusCodes;

class EntityManager extends NgsiLdManager
{
    public function create(Entity $entity): void
    {
        global $systemEntityManager;

        $request = new RequestHelper();
        $request->setMethod(HttpRequestMethods::POST);
        $request->setUrl("{$this->contextBroker->getUrl()}/ngsi-ld/v1/entityOperations/create");
        $request->setHeader("Accept", MimeType::Json->value);
        $request->setHeader("Content-Type", MimeType::Json->value);
        $request->setHeader("Link", Utils::buildLinkHeader($this->workspace->getContextUrl()));
        $request->setJsonBody([$entity], JSON_UNESCAPED_SLASHES);
        $request->setTimeout($_ENV["REQUESTS_TIMEOUT"]);
        if ($this->contextBroker->disableCertificateVerification) {
            $request->setDisableCertificateVerification(true);
        }

        if ($this->service->authorizationRequired) {
            $request->setHeader("Authorization", $this->authorizationHeader);
        }
        if ($this->contextBroker->multiTenancyEnabled && !is_null($this->workspace->contextBrokerTenant)) {
            $request->setHeader("NGSILD-Tenant", $this->workspace->contextBrokerTenant);
        }
        if ($this->contextBroker->customHeaders) {
            foreach ($this->contextBroker->customHeaders as $name => $value) {
                $request->setHeader($name, $value);
            }
        }

        $response = $request->send();

        if ($response->getStatusCode() !== HttpResponseStatusCodes::HTTP_CREATED) {
            throw new EntityManagerException\CreationException($response);
        }

        if ($this->contextBroker->implementationName === ContextBrokerImplementationName::OrionLd->value && isset($this->workspace->temporalServices) && $this->workspace->temporalServices) {
            $temporalServiceManager = new TemporalServiceManager($systemEntityManager);

            $enableTemporalHistory = false;
            foreach ($this->workspace->temporalServices as $temporalServiceId) {
                $temporalService = $temporalServiceManager->readOne($temporalServiceId);
                if ($temporalService->temporalServiceType === TemporalService::Mintaka->value) $enableTemporalHistory = true;
            }

            if ($enableTemporalHistory) {
                $request->setUrl("{$this->contextBroker->getUrl()}/ngsi-ld/v1/temporal/entities");
                $request->setJsonBody($entity, JSON_UNESCAPED_SLASHES);

                $response = $request->send();

                if ($response->getStatusCode() !== HttpResponseStatusCodes::HTTP_NO_CONTENT) {
                    throw new EntityManagerException\CreationException($response);
                }
            }
        }
    }

    public function readOne(string $id): Entity
    {
        if ($id === "") {
            throw new EntityManagerException\EmptyIdException();
        }

        $request = new RequestHelper();
        $request->setMethod(HttpRequestMethods::GET);
        $request->setUrl("{$this->contextBroker->getUrl()}/ngsi-ld/v1/entities");
        $request->setUrlQueryParameter("id", $id);
        $request->setUrlQueryParameter("type", Utils::extractTypeFromNgsiLdUrn($id));
        $request->setHeader("Accept", MimeType::Json->value);
        $request->setHeader("Link", Utils::buildLinkHeader($this->workspace->getContextUrl()));
        $request->setTimeout($_ENV["REQUESTS_TIMEOUT"]);
        if ($this->contextBroker->disableCertificateVerification) {
            $request->setDisableCertificateVerification(true);
        }

        if ($this->service->authorizationRequired) {
            $request->setHeader("Authorization", $this->authorizationHeader);
        }
        if ($this->contextBroker->multiTenancyEnabled && !is_null($this->workspace->contextBrokerTenant)) {
            $request->setHeader("NGSILD-Tenant", $this->workspace->contextBrokerTenant);
        }
        if ($this->contextBroker->customHeaders) {
            foreach ($this->contextBroker->customHeaders as $name => $value) {
                $request->setHeader($name, $value);
            }
        }

        $response = $request->send();

        if ($response->getError()) {
            throw new EntityManagerException\RetrievalException($response);
        }

        $data = $response->getDecodedJsonBody();

        if (!$data) {
            throw new EntityManagerException\RetrievalException($response);
        }

        return new Entity($data[0]);
    }

    public function readMultiple(?string $id = null, ?string $type = null, ?string $query = null, ?int $limit = null, ?int $offset = null): array
    {
        $request = new RequestHelper();
        $request->setMethod(HttpRequestMethods::GET);
        $request->setUrl("{$this->contextBroker->getUrl()}/ngsi-ld/v1/entities");
        $request->setHeader("Accept", MimeType::Json->value);
        $request->setHeader("Link", Utils::buildLinkHeader($this->workspace->getContextUrl()));
        $request->setTimeout($_ENV["REQUESTS_TIMEOUT"]);
        if ($this->contextBroker->disableCertificateVerification) {
            $request->setDisableCertificateVerification(true);
        }

        if ($this->service->authorizationRequired) {
            $request->setHeader("Authorization", $this->authorizationHeader);
        }
        if ($this->contextBroker->multiTenancyEnabled && !is_null($this->workspace->contextBrokerTenant)) {
            $request->setHeader("NGSILD-Tenant", $this->workspace->contextBrokerTenant);
        }
        if ($this->contextBroker->customHeaders) {
            foreach ($this->contextBroker->customHeaders as $name => $value) {
                $request->setHeader($name, $value);
            }
        }

        if (!is_null($id)) {
            $request->setUrlQueryParameter("id", $id);
        }
        if (!is_null($type)) {
            $request->setUrlQueryParameter("type", $type);
        }
        if (!is_null($query)) {
            $request->setUrlQueryParameter("q", $query);
        }
        if (!is_null($limit)) {
            $request->setUrlQueryParameter("limit", $limit);
        }
        if (!is_null($offset)) {
            $request->setUrlQueryParameter("offset", $offset);
        }

        $autoPaginationEnabled = is_null($limit) && is_null($offset);

        if ($autoPaginationEnabled) {
            $autoPaginationLimit = $this->contextBroker->paginationMaxLimit;
            $autoPaginationOffset = 0;
        }

        $entities = [];
        while (true) {
            if ($autoPaginationEnabled) {
                $request->setUrlQueryParameter("limit", $autoPaginationLimit);
                $request->setUrlQueryParameter("offset", $autoPaginationOffset);
            }

            $response = $request->send();

            if ($response->getError()) {
                throw new EntityManagerException\RetrievalException($response);
            }

            $array = $response->getDecodedJsonBody();

            foreach ($array as $element) {
                $entities[] = new Entity($element);
            }

            if ($autoPaginationEnabled) {
                if (count($array) === $autoPaginationLimit) {
                    $autoPaginationOffset += $autoPaginationLimit;
                    continue;
                }
            }

            break;
        }

        return $entities;
    }

    public function update(Entity $entity): void
    {
        $request = new RequestHelper();
        $request->setMethod(HttpRequestMethods::POST);
        $request->setUrl("{$this->contextBroker->getUrl()}/ngsi-ld/v1/entityOperations/upsert");
        $request->setHeader("Accept", MimeType::Json->value);
        $request->setHeader("Content-Type", MimeType::Json->value);
        $request->setHeader("Link", Utils::buildLinkHeader($this->workspace->getContextUrl()));
        $request->setJsonBody([$entity], JSON_UNESCAPED_SLASHES);
        $request->setTimeout($_ENV["REQUESTS_TIMEOUT"]);
        if ($this->contextBroker->disableCertificateVerification) {
            $request->setDisableCertificateVerification(true);
        }

        if ($this->service->authorizationRequired) {
            $request->setHeader("Authorization", $this->authorizationHeader);
        }
        if ($this->contextBroker->multiTenancyEnabled && !is_null($this->workspace->contextBrokerTenant)) {
            $request->setHeader("NGSILD-Tenant", $this->workspace->contextBrokerTenant);
        }
        if ($this->contextBroker->customHeaders) {
            foreach ($this->contextBroker->customHeaders as $name => $value) {
                $request->setHeader($name, $value);
            }
        }

        $response = $request->send();

        if ($response->getStatusCode() !== HttpResponseStatusCodes::HTTP_NO_CONTENT) {
            throw new EntityManagerException\UpdateException($response);
        }
    }

    public function updateLegacy(Entity $entity): void
    {
        $request = new RequestHelper();
        $request->setMethod(HttpRequestMethods::POST);
        $request->setUrl("{$this->contextBroker->getUrl()}/ngsi-ld/v1/entities/{$entity->getId()}/attrs");
        $request->setHeader("Accept", MimeType::Json->value);
        $request->setHeader("Content-Type", MimeType::Json->value);
        $request->setHeader("Link", Utils::buildLinkHeader($this->workspace->getContextUrl()));
        $request->setJsonBody(array_diff_key((array) $entity, array_flip(["id"])), JSON_UNESCAPED_SLASHES);
        $request->setTimeout($_ENV["REQUESTS_TIMEOUT"]);
        if ($this->contextBroker->disableCertificateVerification) {
            $request->setDisableCertificateVerification(true);
        }

        if ($this->service->authorizationRequired) {
            $request->setHeader("Authorization", $this->authorizationHeader);
        }
        if ($this->contextBroker->multiTenancyEnabled && !is_null($this->workspace->contextBrokerTenant)) {
            $request->setHeader("NGSILD-Tenant", $this->workspace->contextBrokerTenant);
        }
        if ($this->contextBroker->customHeaders) {
            foreach ($this->contextBroker->customHeaders as $name => $value) {
                $request->setHeader($name, $value);
            }
        }

        $response = $request->send();

        if ($response->getStatusCode() !== HttpResponseStatusCodes::HTTP_NO_CONTENT) {
            throw new EntityManagerException\UpdateException($response);
        }
    }

    public function delete(Entity $entity): void
    {
        $request = new RequestHelper();
        $request->setMethod(HttpRequestMethods::POST);
        $request->setUrl("{$this->contextBroker->getUrl()}/ngsi-ld/v1/entityOperations/delete");
        $request->setHeader("Accept", MimeType::Json->value);
        $request->setHeader("Content-Type", MimeType::Json->value);
        $request->setHeader("Link", Utils::buildLinkHeader($this->workspace->getContextUrl()));
        $request->setJsonBody([$entity->getId()], JSON_UNESCAPED_SLASHES);
        $request->setTimeout($_ENV["REQUESTS_TIMEOUT"]);
        if ($this->contextBroker->disableCertificateVerification) {
            $request->setDisableCertificateVerification(true);
        }

        if ($this->service->authorizationRequired) {
            $request->setHeader("Authorization", $this->authorizationHeader);
        }
        if ($this->contextBroker->multiTenancyEnabled && !is_null($this->workspace->contextBrokerTenant)) {
            $request->setHeader("NGSILD-Tenant", $this->workspace->contextBrokerTenant);
        }
        if ($this->contextBroker->customHeaders) {
            foreach ($this->contextBroker->customHeaders as $name => $value) {
                $request->setHeader($name, $value);
            }
        }

        $response = $request->send();

        if ($response->getStatusCode() !== HttpResponseStatusCodes::HTTP_NO_CONTENT) {
            throw new EntityManagerException\DeletionException($response);
        }
    }
}
