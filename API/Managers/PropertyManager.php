<?php

namespace API\Managers;

use API\Models\Property;

class PropertyManager
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create(Property $property): void
    {
        $entity = $property->toEntity();
        $this->entityManager->create($entity);
    }

    public function readOne(string $id): Property
    {
        $entity = $this->entityManager->readOne($id);
        $property = new Property();
        $property->fromEntity($entity);
        return $property;
    }

    public function readMultiple(?string $query = null, bool $idAsKey = false): array
    {
        $entities = $this->entityManager->readMultiple(null, Property::TYPE, $query);

        $properties = [];
        foreach ($entities as $entity) {
            $property = new Property();
            $property->fromEntity($entity);
            if ($idAsKey) $properties[$property->id] = $property;
            else $properties[] = $property;
        }

        return $properties;
    }

    public function update(Property $property): void
    {
        $entity = $property->toEntity();
        $this->entityManager->update($entity);
    }

    public function delete(Property $property): void
    {
        $entity = $property->toEntity();
        $this->entityManager->delete($entity);
    }
}
