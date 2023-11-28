<?php

namespace API\Managers;

use API\Models\ControlledProperty;

class ControlledPropertyManager
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create(ControlledProperty $controlledProperty): void
    {
        $entity = $controlledProperty->toEntity();
        $this->entityManager->create($entity);
    }

    public function readOne(string $id): ControlledProperty
    {
        $entity = $this->entityManager->readOne($id);
        $controlledProperty = new ControlledProperty();
        $controlledProperty->fromEntity($entity);
        return $controlledProperty;
    }

    public function readMultiple(?string $query = null, bool $idAsKey = false): array
    {
        $entities = $this->entityManager->readMultiple(null, ControlledProperty::TYPE, $query);

        $controlledProperties = [];
        foreach ($entities as $entity) {
            $controlledProperty = new ControlledProperty();
            $controlledProperty->fromEntity($entity);
            if ($idAsKey) $controlledProperties[$controlledProperty->id] = $controlledProperty;
            else $controlledProperties[] = $controlledProperty;
        }

        return $controlledProperties;
    }

    public function update(ControlledProperty $controlledProperty): void
    {
        $entity = $controlledProperty->toEntity();
        $this->entityManager->update($entity);
    }

    public function delete(ControlledProperty $controlledProperty): void
    {
        $entity = $controlledProperty->toEntity();
        $this->entityManager->delete($entity);
    }
}
