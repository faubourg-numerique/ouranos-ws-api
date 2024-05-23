<?php

namespace API\Managers;

use API\Enums\MimeType;
use API\Exceptions\ManagerException\TemporalEntityPropertyManagerException;
use API\Models\ContextBroker;
use API\Models\IdentityManager;
use API\Models\IdentityManagerGrant;
use API\Models\Property;
use API\Models\Service;
use API\Models\TemporalEntityProperty;
use API\Models\TemporalService;
use API\Models\Workspace;
use API\StaticClasses\Authorization;
use API\StaticClasses\Utils;
use Core\Helpers\RequestHelper;
use Core\HttpRequestMethods;

class TemporalEntityPropertyManager
{
    const DATE_FORMAT = "Y-m-d\TH:i:s\Z";

    private TemporalService\NgsiLd|TemporalService\Mintaka $temporalService;
    private ContextBroker $contextBroker;
    private Service $service;
    private Workspace $workspace;
    private ?string $authorizationHeader = null;
    private ?string $temporalServiceAuthorizationHeader = null;

    public function __construct(TemporalService\NgsiLd|TemporalService\Mintaka $temporalService, Workspace $workspace, Service $service, ContextBroker $contextBroker, ?IdentityManager $identityManager = null, ?IdentityManagerGrant $identityManagerGrant = null, ?IdentityManager $temporalServiceIdentityManager = null, ?IdentityManagerGrant $temporalServiceIdentityManagerGrant = null)
    {
        $this->temporalService = $temporalService;
        $this->contextBroker = $contextBroker;
        $this->service = $service;
        $this->workspace = $workspace;

        if ($service->authorizationRequired) {
            if (isset($service->authorizationMode) && $service->authorizationMode == "siop2") {
                $this->authorizationHeader = API::request()->getHeader("Gateway-Authorization");
            } else {
                $accessToken = Authorization::getAccessToken($identityManager, $identityManagerGrant);
                $this->authorizationHeader = "Bearer {$accessToken}";
            }
        }

        if (isset($temporalService->authorizationRequired) && $temporalService->authorizationRequired) {
            if ($temporalService->authorizationMode && $temporalService->authorizationMode == "siop2") {
                $this->temporalServiceAuthorizationHeader = API::request()->getHeader("Gateway-Authorization");
            } else {
                $accessToken = Authorization::getAccessToken($temporalServiceIdentityManager, $temporalServiceIdentityManagerGrant);
                $this->temporalServiceAuthorizationHeader = "Bearer {$accessToken}";
            }
        }
    }

    public function read(TemporalEntityProperty $temporalEntityProperty): TemporalEntityProperty
    {
        if ($temporalEntityProperty->id === "") {
            throw new TemporalEntityPropertyManagerException\EmptyIdException();
        }

        switch ($this->temporalService->temporalServiceType) {
            case "ngsi-ld": {
                    $request = new RequestHelper();
                    $request->setMethod(HttpRequestMethods::GET);
                    $request->setUrl("{$this->contextBroker->getUrl()}/ngsi-ld/v1/temporal/entities/{$temporalEntityProperty->id}");
                    $request->setUrlQueryParameter("attrs", $temporalEntityProperty->name);
                    $request->setHeader("Accept", MimeType::Json->value);
                    $request->setHeader("Link", Utils::buildLinkHeader($this->workspace->getContextUrl()));
                    $request->setTimeout($_ENV["REQUESTS_TIMEOUT"]);
                    if ($this->contextBroker->disableCertificateVerification) {
                        $request->setDisableCertificateVerification(true);
                    }

                    if (!is_null($temporalEntityProperty->fromTime) && !is_null($temporalEntityProperty->toTime)) {
                        $request->setUrlQueryParameter("timerel", "between");
                        $request->setUrlQueryParameter("timeAt", gmdate(self::DATE_FORMAT, $temporalEntityProperty->fromTime));
                        $request->setUrlQueryParameter("endTimeAt", gmdate(self::DATE_FORMAT, $temporalEntityProperty->toTime));
                    } elseif (!is_null($temporalEntityProperty->fromTime)) {
                        $request->setUrlQueryParameter("timerel", "after");
                        $request->setUrlQueryParameter("timeAt", gmdate(self::DATE_FORMAT, $temporalEntityProperty->fromTime));
                    } elseif (!is_null($temporalEntityProperty->toTime)) {
                        $request->setUrlQueryParameter("timerel", "before");
                        $request->setUrlQueryParameter("timeAt", gmdate(self::DATE_FORMAT, $temporalEntityProperty->toTime));
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
                        throw new TemporalEntityPropertyManagerException\RetrievalException($response);
                    }

                    $temporalEntityProperty->data = [];

                    $data = $response->getDecodedJsonBody();
                    if (isset($data[$temporalEntityProperty->name]) && is_array($data[$temporalEntityProperty->name])) {
                        $temporalEntityProperty->data = $data[$temporalEntityProperty->name];
                    }

                    return $temporalEntityProperty;
                }
            case "mintaka": {
                    $request = new RequestHelper();
                    $request->setMethod(HttpRequestMethods::GET);
                    $request->setUrl("{$this->temporalService->getUrl()}/temporal/entities/{$temporalEntityProperty->id}");
                    $request->setUrlQueryParameter("attrs", $temporalEntityProperty->name);
                    $request->setHeader("Accept", MimeType::Json->value);
                    $request->setHeader("Link", Utils::buildLinkHeader($this->workspace->getContextUrl()));
                    $request->setTimeout($_ENV["REQUESTS_TIMEOUT"]);
                    if ($this->temporalService->disableCertificateVerification) {
                        $request->setDisableCertificateVerification(true);
                    }

                    if (!is_null($temporalEntityProperty->fromTime) && !is_null($temporalEntityProperty->toTime)) {
                        $request->setUrlQueryParameter("timerel", "between");
                        $request->setUrlQueryParameter("timeAt", gmdate(self::DATE_FORMAT, $temporalEntityProperty->fromTime));
                        $request->setUrlQueryParameter("endTimeAt", gmdate(self::DATE_FORMAT, $temporalEntityProperty->toTime));
                    } elseif (!is_null($temporalEntityProperty->fromTime)) {
                        $request->setUrlQueryParameter("timerel", "after");
                        $request->setUrlQueryParameter("timeAt", gmdate(self::DATE_FORMAT, $temporalEntityProperty->fromTime));
                    } elseif (!is_null($temporalEntityProperty->toTime)) {
                        $request->setUrlQueryParameter("timerel", "before");
                        $request->setUrlQueryParameter("timeAt", gmdate(self::DATE_FORMAT, $temporalEntityProperty->toTime));
                    }

                    if ($this->temporalService->authorizationRequired) {
                        $request->setHeader("Authorization", $this->temporalServiceAuthorizationHeader);
                    }
                    if ($this->contextBroker->multiTenancyEnabled && !is_null($this->workspace->contextBrokerTenant)) {
                        $request->setHeader("NGSILD-Tenant", $this->workspace->contextBrokerTenant);
                    }

                    $response = $request->send();

                    if ($response->getError()) {
                        throw new TemporalEntityPropertyManagerException\RetrievalException($response);
                    }

                    $temporalEntityProperty->data = [];

                    $data = $response->getDecodedJsonBody();
                    if (isset($data[$temporalEntityProperty->name]) && is_array($data[$temporalEntityProperty->name])) {
                        $temporalEntityProperty->data = $data[$temporalEntityProperty->name];
                    }

                    if ($response->getStatusCode() === 206 && $response->getHeader("Content-Range")) {
                        $temp = $response->getHeader("Content-Range");
                        $temp = explode(" ", $temp)[1];
                        $temp = strtok($temp, "/");
                        $temp = explode("-", $temp);
                        $fromDate = implode("-", [$temp[0], $temp[1], $temp[2]]);
                        $toDate = implode("-", [$temp[3], $temp[4], $temp[5]]);
                        $temporalEntityProperty->fromTime = strtotime($fromDate);
                        $temporalEntityProperty->toTime = strtotime($toDate);
                    }

                    return $temporalEntityProperty;
                }
            default: {
                    throw new TemporalEntityPropertyManagerException\NotSupportedTypeException();
                    break;
                }
        }
    }
}
