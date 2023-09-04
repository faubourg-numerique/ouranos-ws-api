<?php

namespace API\Managers;

use API\StaticClasses\Utils;
use API\Enums\MimeType;
use API\Exceptions\ManagerException\SubscriptionManagerException;
use API\Models\Subscription;
use Core\Helpers\RequestHelper;
use Core\HttpRequestMethods;

class SubscriptionManager extends NgsiLdManager
{
    public function create(Subscription $subscription): void
    {
        $request = new RequestHelper();
        $request->setMethod(HttpRequestMethods::POST);
        $request->setUrl("{$this->contextBroker->getUrl()}/ngsi-ld/v1/subscriptions");
        $request->setHeader("Accept", MimeType::Json->value);
        $request->setHeader("Content-Type", MimeType::Json->value);
        $request->setHeader("Link", Utils::buildLinkHeader($this->workspace->getContextUrl()));
        $request->setJsonBody($subscription, JSON_UNESCAPED_SLASHES);
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
            throw new SubscriptionManagerException\CreationException($response);
        }
    }

    public function readOne(string $id): Subscription
    {
        if ($id === "") {
            throw new SubscriptionManagerException\EmptyIdException();
        }

        $request = new RequestHelper();
        $request->setMethod(HttpRequestMethods::GET);
        $request->setUrl("{$this->contextBroker->getUrl()}/ngsi-ld/v1/subscriptions/{$id}");
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
            throw new SubscriptionManagerException\RetrievalException($response);
        }

        $data = $response->getDecodedJsonBody();
        return new Subscription($data);
    }

    public function readMultiple(): array
    {
        $request = new RequestHelper();
        $request->setMethod(HttpRequestMethods::GET);
        $request->setUrl("{$this->contextBroker->getUrl()}/ngsi-ld/v1/subscriptions");
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

        $autoPaginationEnabled = true;

        if ($autoPaginationEnabled) {
            $autoPaginationLimit = $this->contextBroker->paginationMaxLimit;
            $autoPaginationOffset = 0;
        }

        $subscriptions = [];
        while (true) {
            if ($autoPaginationEnabled) {
                $request->setUrlQueryParameter("limit", $autoPaginationLimit);
                $request->setUrlQueryParameter("offset", $autoPaginationOffset);
            }

            $response = $request->send();

            if ($response->getError()) {
                throw new SubscriptionManagerException\RetrievalException($response);
            }

            $array = $response->getDecodedJsonBody();

            foreach ($array as $element) {
                $subscriptions[] = new Subscription($element);
            }

            if ($autoPaginationEnabled) {
                if (count($array) === $autoPaginationLimit) {
                    $autoPaginationOffset += $autoPaginationLimit;
                    continue;
                }
            }

            break;
        }

        return $subscriptions;
    }

    public function delete(Subscription $subscription): void
    {
        $request = new RequestHelper();
        $request->setMethod(HttpRequestMethods::DELETE);
        $request->setUrl("{$this->contextBroker->getUrl()}/ngsi-ld/v1/subscriptions/{$subscription->getId()}");
        $request->setHeader("Accept", MimeType::Json->value);
        $request->setHeader("Content-Type", MimeType::Json->value);
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
            throw new SubscriptionManagerException\DeletionException($response);
        }
    }
}
