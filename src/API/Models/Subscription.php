<?php

namespace API\Models;

use Core\Model;

class Subscription extends Model
{
    const TYPE = "Subscription";

    public string $id;
    public string $type;
    public string $subscriptionName;
    public string $description;
    public array $entities;
    public array $watchedAttributes;
    public int $timeInterval;
    public string $q;
    public array $geoQ;
    public bool $isActive;
    public array $notification = [];
    public string $expiresAt;
    public int $throttling;
    public string $lang;

    public function __construct(?array $data = null)
    {
        $this->type = self::TYPE;

        parent::__construct($data);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function setSubscriptionName(string $subscriptionName): void
    {
        $this->subscriptionName = $subscriptionName;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function addEntity(?string $id = null, ?string $idPattern = null, ?string $type = null): void
    {
        if (!isset($this->entities)) {
            $this->entities = [];
        }

        $entity = [];
        if (!is_null($id)) {
            $entity["id"] = $id;
        }
        if (!is_null($idPattern)) {
            $entity["idPattern"] = $idPattern;
        }
        if (!is_null($type)) {
            $entity["type"] = $type;
        }

        $this->entities[] = $entity;
    }

    public function addWatchedAttribute(string $watchedAttribute): void
    {
        if (!isset($this->watchedAttributes)) {
            $this->watchedAttributes = [];
        }

        $this->watchedAttributes[] = $watchedAttribute;
    }

    public function setTimeInterval(int $timeInterval): void
    {
        $this->timeInterval = $timeInterval;
    }

    public function setQuery(string $query): void
    {
        $this->q = $query;
    }

    public function setGeoQuery(?string $geometry = null, ?array $coordinates = null, ?string $georel = null, ?string $geoproperty = null): void
    {
        $this->geoQ = [];
        if (!is_null($geometry)) {
            $this->geoQ["geometry"] = $geometry;
        }
        if (!is_null($coordinates)) {
            $this->geoQ["coordinates"] = $coordinates;
        }
        if (!is_null($georel)) {
            $this->geoQ["georel"] = $georel;
        }
        if (!is_null($geoproperty)) {
            $this->geoQ["geoproperty"] = $geoproperty;
        }
    }

    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    public function addNotificationAttribute(string $notificationAttribute): void
    {
        if (!isset($this->notification["attributes"])) {
            $this->notification["attributes"] = [];
        }

        $this->notification["attributes"][] = $notificationAttribute;
    }

    public function setNotificationFormat(string $notificationFormat): void
    {
        $this->notification["format"] = $notificationFormat;
    }

    public function setNotificationEndpoint(string $uri, ?string $accept = null, ?array $receiverInfo = null, ?array $notifierInfo = null): void
    {
        $this->notification["endpoint"] = [
            "uri" => $uri
        ];

        if (!is_null($accept)) {
            $this->notification["endpoint"]["accept"] = $accept;
        }
        if (!is_null($receiverInfo)) {
            $this->notification["endpoint"]["receiverInfo"] = $receiverInfo;
        }
        if (!is_null($notifierInfo)) {
            $this->notification["endpoint"]["notifierInfo"] = $notifierInfo;
        }
    }

    public function setExpiresAt(string $expiresAt): void
    {
        $this->expiresAt = $expiresAt;
    }

    public function setThrottling(int $throttling): void
    {
        $this->throttling = $throttling;
    }

    public function setLang(string $lang): void
    {
        $this->lang = $lang;
    }
}
