<?php

namespace API\Managers;

use API\Models\Type;

class TypeManager
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create(Type $type): void
    {
        $entity = $type->toEntity();
        $this->entityManager->create($entity);
    }

    public function readOne(string $id): Type
    {
        $entity = $this->entityManager->readOne($id);
        $type = new Type();
        $type->fromEntity($entity);
        return $type;
    }

    public function readMultiple(?string $query = null, bool $idAsKey = false): array
    {
        $entities = $this->entityManager->readMultiple(null, Type::TYPE, $query);

        $types = [];
        foreach ($entities as $entity) {
            $type = new Type();
            $type->fromEntity($entity);
            if ($idAsKey) $types[$type->id] = $type;
            else $types[] = $type;
        }

        return $types;
    }

    public function update(Type $type): void
    {
        $entity = $type->toEntity();
        $this->entityManager->update($entity);
    }

    public function delete(Type $type): void
    {
        $entity = $type->toEntity();
        $this->entityManager->delete($entity);
    }
}
