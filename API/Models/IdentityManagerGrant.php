<?php

namespace API\Models;

use API\Traits\Updatable;
use Core\Model;
use Defuse\Crypto\Crypto;

class IdentityManagerGrant extends Model
{
    use Updatable;

    const TYPE = "IdentityManagerGrant";

    public string $id;
    public string $name;
    public ?string $description = null;
    public string $grantType;
    public ?string $clientId;
    public ?string $clientSecret;
    public ?string $username = null;
    public ?string $password = null;

    public function toEntity(): Entity
    {
        global $encryptionKey;

        $entity = new Entity();
        $entity->setId($this->id);
        $entity->setType(self::TYPE);
        $entity->setProperty("name", $this->name);
        if (!is_null($this->description)) {
            $entity->setProperty("description", $this->description);
        }
        $entity->setProperty("grantType", $this->grantType);
        $entity->setProperty("clientId", Crypto::encrypt(strval($this->clientId), $encryptionKey, false));
        $entity->setProperty("clientSecret", Crypto::encrypt(strval($this->clientSecret), $encryptionKey, false));
        if (!is_null($this->username)) {
            $entity->setProperty("username", Crypto::encrypt(strval($this->username), $encryptionKey, false));
        }
        if (!is_null($this->password)) {
            $entity->setProperty("password", Crypto::encrypt(strval($this->password), $encryptionKey, false));
        }
        return $entity;
    }

    public function fromEntity(Entity $entity): void
    {
        global $encryptionKey;

        $this->id = $entity->getId();
        $this->name = $entity->getProperty("name");
        if ($entity->propertyExists("description")) {
            $this->description = $entity->getProperty("description");
        }
        $this->grantType = $entity->getProperty("grantType");
        $this->clientId = Crypto::decrypt($entity->getProperty("clientId"), $encryptionKey, false);
        $this->clientSecret = Crypto::decrypt($entity->getProperty("clientSecret"), $encryptionKey, false);
        if ($entity->propertyExists("username")) {
            $this->username = Crypto::decrypt($entity->getProperty("username"), $encryptionKey, false);
        }
        if ($entity->propertyExists("password")) {
            $this->password = Crypto::decrypt($entity->getProperty("password"), $encryptionKey, false);
        }
    }
}
