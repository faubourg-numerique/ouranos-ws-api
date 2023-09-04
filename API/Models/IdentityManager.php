<?php

namespace API\Models;

use API\StaticClasses\Utils;
use API\Exceptions\ModelsExceptions\IdentityManagerException;
use API\Traits\Updatable;
use Core\Helpers\RequestHelper;
use Core\HttpRequestMethods;
use Core\Model;

class IdentityManager extends Model
{
    use Updatable;

    const TYPE = "IdentityManager";

    public string $id;
    public string $name;
    public ?string $description = null;
    public string $scheme;
    public string $host;
    public int $port;
    public ?string $path = null;
    public ?string $oauth2TokenPath = null;
    public ?string $userPath = null;
    public string $implementationName;
    public string $implementationVersion;
    public ?bool $disableCertificateVerification = null;

    public function getUrl(): string
    {
        return Utils::buildUrl($this->scheme, $this->host, $this->port, $this->path);
    }

    public function getOauth2TokenUrl(): string
    {
        return $this->getUrl() . $this->oauth2TokenPath;
    }

    public function getUserUrl(): string
    {
        return $this->getUrl() . $this->userPath;
    }

    public function toEntity(): Entity
    {
        $entity = new Entity();
        $entity->setId($this->id);
        $entity->setType(self::TYPE);
        $entity->setProperty("name", $this->name);
        if (!is_null($this->description)) {
            $entity->setProperty("description", $this->description);
        }
        $entity->setProperty("scheme", $this->scheme);
        $entity->setProperty("host", $this->host);
        $entity->setProperty("port", $this->port);
        if (!is_null($this->path)) {
            $entity->setProperty("path", $this->path);
        }
        if (!is_null($this->oauth2TokenPath)) {
            $entity->setProperty("oauth2TokenPath", $this->oauth2TokenPath);
        }
        if (!is_null($this->userPath)) {
            $entity->setProperty("userPath", $this->userPath);
        }
        $entity->setProperty("implementationName", $this->implementationName);
        $entity->setProperty("implementationVersion", $this->implementationVersion);
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
        $this->scheme = $entity->getProperty("scheme");
        $this->host = $entity->getProperty("host");
        $this->port = $entity->getProperty("port");
        if ($entity->propertyExists("path")) {
            $this->path = $entity->getProperty("path");
        }
        if ($entity->propertyExists("oauth2TokenPath")) {
            $this->oauth2TokenPath = $entity->getProperty("oauth2TokenPath");
        }
        if ($entity->propertyExists("userPath")) {
            $this->userPath = $entity->getProperty("userPath");
        }
        $this->implementationName = $entity->getProperty("implementationName");
        $this->implementationVersion = $entity->getProperty("implementationVersion");
        if ($entity->propertyExists("disableCertificateVerification")) {
            $this->disableCertificateVerification = $entity->getProperty("disableCertificateVerification");
        }
    }
}
