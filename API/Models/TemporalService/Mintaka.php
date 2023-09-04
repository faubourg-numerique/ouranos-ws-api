<?php

namespace API\Models\TemporalService;

use API\Models\Entity;
use API\Models\TemporalService;
use API\StaticClasses\Utils;

class Mintaka extends TemporalService
{
    public string $version;
    public string $scheme;
    public string $host;
    public int $port;
    public ?string $path = null;
    public bool $authorizationRequired;
    public ?string $hasIdentityManager = null;
    public ?string $hasIdentityManagerGrant = null;
    public ?bool $disableCertificateVerification = null;

    public function toEntity(): Entity
    {
        $entity = new Entity();
        $entity->setId($this->id);
        $entity->setType(self::TYPE);
        $entity->setProperty("name", $this->name);
        if (!is_null($this->description)) {
            $entity->setProperty("description", $this->description);
        }
        $entity->setProperty("temporalServiceType", $this->temporalServiceType);
        $entity->setProperty("version", $this->version);
        $entity->setProperty("scheme", $this->scheme);
        $entity->setProperty("host", $this->host);
        $entity->setProperty("port", $this->port);
        if (!is_null($this->path)) {
            $entity->setProperty("path", $this->path);
        }
        $entity->setProperty("authorizationRequired", $this->authorizationRequired);
        if (!is_null($this->hasIdentityManager)) {
            $entity->setProperty("hasIdentityManager", $this->hasIdentityManager);
        }
        if (!is_null($this->hasIdentityManagerGrant)) {
            $entity->setProperty("hasIdentityManagerGrant", $this->hasIdentityManagerGrant);
        }
        if (!is_null($this->disableCertificateVerification)) {
            $entity->setProperty("disableCertificateVerification", $this->disableCertificateVerification);
        }
        return $entity;
    }

    public function fromEntity(Entity $entity): void
    {
        $this->id = $entity->getId();
        $this->name = $entity->getProperty("name");
        if ($entity->propertyExists("description")) {
            $this->description = $entity->getProperty("description");
        }
        $this->temporalServiceType = $entity->getProperty("temporalServiceType");
        if ($entity->propertyExists("version")) {
            $this->version = $entity->getProperty("version");
        }
        if ($entity->propertyExists("scheme")) {
            $this->scheme = $entity->getProperty("scheme");
        }
        if ($entity->propertyExists("host")) {
            $this->host = $entity->getProperty("host");
        }
        if ($entity->propertyExists("port")) {
            $this->port = $entity->getProperty("port");
        }
        if ($entity->propertyExists("path")) {
            $this->path = $entity->getProperty("path");
        }
        if ($entity->propertyExists("authorizationRequired")) {
            $this->authorizationRequired = $entity->getProperty("authorizationRequired");
        }
        if ($entity->propertyExists("hasIdentityManager")) {
            $this->hasIdentityManager = $entity->getProperty("hasIdentityManager");
        }
        if ($entity->propertyExists("hasIdentityManagerGrant")) {
            $this->hasIdentityManagerGrant = $entity->getProperty("hasIdentityManagerGrant");
        }
        if ($entity->propertyExists("disableCertificateVerification")) {
            $this->disableCertificateVerification = $entity->getProperty("disableCertificateVerification");
        }
    }

    public function getUrl(): string
    {
        return Utils::buildUrl($this->scheme, $this->host, $this->port, $this->path);
    }
}
