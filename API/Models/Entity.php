<?php

namespace API\Models;

use API\Enums\NgsiLdAttributeType;
use API\Enums\NgsiLdGeoPropertyType;

class Entity
{
    public string $id;
    public string $type;

    public function __construct(?array $data = null)
    {
        if (!is_null($data)) {
            foreach ($data as $name => $value) {
                if (is_null($value)) {
                    continue;
                }
                $this->$name = $value;
            }
        }
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    protected function isAttributeValid(mixed $attribute): bool
    {
        if (!isset($attribute)) {
            return false;
        }
        if (!is_array($attribute)) {
            return false;
        }
        if (!array_key_exists("type", $attribute)) {
            return false;
        }
        if (!is_string($attribute["type"])) {
            return false;
        }
        $ngsiLdAttributeType = NgsiLdAttributeType::tryFrom($attribute["type"]);
        if (is_null($ngsiLdAttributeType)) {
            return false;
        }
        switch ($ngsiLdAttributeType) {
            case NgsiLdAttributeType::Property:
                if (!array_key_exists("value", $attribute)) {
                    return false;
                }
                break;
            case NgsiLdAttributeType::GeoProperty:
                if (!array_key_exists("value", $attribute)) {
                    return false;
                }
                if (!is_array($attribute["value"])) {
                    return false;
                }
                if (!array_key_exists("type", $attribute["value"])) {
                    return false;
                }
                if (!is_string($attribute["value"]["type"])) {
                    return false;
                }
                if (is_null(NgsiLdGeoPropertyType::tryFrom($attribute["value"]["type"]))) {
                    return false;
                }
                if (!array_key_exists("coordinates", $attribute["value"])) {
                    return false;
                }
                if (!is_array($attribute["value"]["coordinates"])) {
                    return false;
                }
                break;
            case NgsiLdAttributeType::Relationship:
                if (!array_key_exists("object", $attribute)) {
                    return false;
                }
                if (!is_string($attribute["object"])) {
                    return false;
                }
                break;
        }
        return true;
    }

    protected function isAttributeProperty(array $attribute): bool
    {
        $ngsiLdAttributeType = NgsiLdAttributeType::tryFrom($attribute["type"]);
        return $ngsiLdAttributeType === NgsiLdAttributeType::Property;
    }

    protected function isAttributeGeoProperty(array $attribute): bool
    {
        $ngsiLdAttributeType = NgsiLdAttributeType::tryFrom($attribute["type"]);
        return $ngsiLdAttributeType === NgsiLdAttributeType::GeoProperty;
    }

    protected function isAttributeRelationship(array $attribute): bool
    {
        $ngsiLdAttributeType = NgsiLdAttributeType::tryFrom($attribute["type"]);
        return $ngsiLdAttributeType === NgsiLdAttributeType::Relationship;
    }

    protected function &getAttributeReference(string|array $nameOrNames): ?array
    {
        if (is_string($nameOrNames)) {
            $name = $nameOrNames;
            return $this->$name;
        }
        if (is_array($nameOrNames)) {
            $names = $nameOrNames;
            $name = array_shift($names);
            $attributeReference = &$this->$name;
            foreach ($names as $name) {
                $attributeReference = &$attributeReference[$name];
            }
            return $attributeReference;
        }
    }

    public function getProperty(string|array $nameOrNames): mixed
    {
        $attributeReference = &$this->getAttributeReference($nameOrNames);

        if (!$this->isAttributeValid($attributeReference) || !$this->isAttributeProperty($attributeReference)) {
            return null;
        }

        return $attributeReference["value"];
    }

    public function getGeoProperty(string|array $nameOrNames): ?array
    {
        $attributeReference = &$this->getAttributeReference($nameOrNames);

        if (!$this->isAttributeValid($attributeReference) || !$this->isAttributeGeoProperty($attributeReference)) {
            return null;
        }

        return [$attributeReference["value"]["type"], $attributeReference["value"]["coordinates"]];
    }

    public function getRelationship(string|array $nameOrNames): ?string
    {
        $attributeReference = &$this->getAttributeReference($nameOrNames);

        if (!$this->isAttributeValid($attributeReference) || !$this->isAttributeRelationship($attributeReference)) {
            return null;
        }

        return $attributeReference["object"];
    }

    public function setProperty(string|array $nameOrNames, string|int|float|array $value): void
    {
        $attributeReference = &$this->getAttributeReference($nameOrNames);

        if (is_null($attributeReference) || !$this->isAttributeValid($attributeReference) || !$this->isAttributeProperty($attributeReference)) {
            $attributeReference = [];
        }

        $attributeReference["type"] = NgsiLdAttributeType::Property->value;
        $attributeReference["value"] = $value;
    }

    public function setGeoProperty(string|array $nameOrNames, string $type, array $coordinates): void
    {
        $attributeReference = &$this->getAttributeReference($nameOrNames);

        if (is_null($attributeReference) || !$this->isAttributeValid($attributeReference) || !$this->isAttributeGeoProperty($attributeReference)) {
            $attributeReference = [];
        }

        $attributeReference["type"] = NgsiLdAttributeType::GeoProperty->value;
        $attributeReference["value"] = [
            "type" => $type,
            "coordinates" => $coordinates
        ];
    }

    public function setRelationship(string|array $nameOrNames, string $object): void
    {
        $attributeReference = &$this->getAttributeReference($nameOrNames);

        if (is_null($attributeReference) || !$this->isAttributeValid($attributeReference) || !$this->isAttributeRelationship($attributeReference)) {
            $attributeReference = [];
        }

        $attributeReference["type"] = NgsiLdAttributeType::Relationship->value;
        $attributeReference["object"] = $object;
    }

    public function deleteAttribute(string|array $nameOrNames): void
    {
        if (is_string($nameOrNames)) {
            $name = $nameOrNames;
            unset($this->$name);
        }
        if (is_array($nameOrNames)) {
            $names = $nameOrNames;
            $name = array_shift($names);
            if (!$names) {
                unset($this->$name);
                return;
            }
            $names = array_map("addslashes", $names);
            eval('unset($this->$name["' . implode('"]["', $names) . '"]);');
        }
    }

    public function attributeExists(string|array $nameOrNames): bool
    {
        $attributeReference = &$this->getAttributeReference($nameOrNames);
        return isset($attributeReference);
    }

    public function propertyExists(string|array $nameOrNames): bool
    {
        if (!$this->attributeExists($nameOrNames)) return false;
        $attributeReference = &$this->getAttributeReference($nameOrNames);
        if (!$this->isAttributeValid($attributeReference)) return false;
        return $this->isAttributeProperty($attributeReference);
    }

    public function geoPropertyExists(string|array $nameOrNames): bool
    {
        if (!$this->attributeExists($nameOrNames)) return false;
        $attributeReference = &$this->getAttributeReference($nameOrNames);
        if (!$this->isAttributeValid($attributeReference)) return false;
        return $this->isAttributeGeoProperty($attributeReference);
    }

    public function relationshipExists(string|array $nameOrNames): bool
    {
        if (!$this->attributeExists($nameOrNames)) return false;
        $attributeReference = &$this->getAttributeReference($nameOrNames);
        if (!$this->isAttributeValid($attributeReference)) return false;
        return $this->isAttributeRelationship($attributeReference);
    }

    public function setAttributeMetadata(string|array $nameOrNames, string $name, string $value): void
    {
        if (!$this->attributeExists($nameOrNames)) return;
        $attributeReference = &$this->getAttributeReference($nameOrNames);
        if (!$this->isAttributeValid($attributeReference)) return;
        $attributeReference[$name] = $value;
    }
}
