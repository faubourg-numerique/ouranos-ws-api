<?php

namespace API\Managers;

use API\Models\WoTProperty;

class WoTPropertyManager
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create(WoTProperty $woTProperty): void
    {
        $entity = $woTProperty->toEntity();
        $this->entityManager->create($entity);
    }

    public function readOne(string $id): WoTProperty
    {
        $entity = $this->entityManager->readOne($id);
        $woTProperty = new WoTProperty();
        $woTProperty->fromEntity($entity);
        return $woTProperty;
    }

    public function readMultiple(?string $query = null, bool $idAsKey = false): array
    {
        $entities = $this->entityManager->readMultiple(null, WoTProperty::TYPE, $query);

        $woTProperties = [];
        foreach ($entities as $entity) {
            $woTProperty = new WoTProperty();
            $woTProperty->fromEntity($entity);
            if ($idAsKey) $woTProperties[$woTProperty->id] = $woTProperty;
            else $woTProperties[] = $woTProperty;
        }

        return $woTProperties;
    }

    public function update(WoTProperty $woTProperty): void
    {
        $entity = $woTProperty->toEntity();
        $this->entityManager->update($entity);
    }

    public function delete(WoTProperty $woTProperty): void
    {
        $entity = $woTProperty->toEntity();
        $this->entityManager->delete($entity);
    }
}
