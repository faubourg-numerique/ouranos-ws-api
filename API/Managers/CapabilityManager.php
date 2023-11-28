<?php

namespace API\Managers;

use API\Models\Capability;

class CapabilityManager
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create(Capability $capability): void
    {
        $entity = $capability->toEntity();
        $this->entityManager->create($entity);
    }

    public function readOne(string $id): Capability
    {
        $entity = $this->entityManager->readOne($id);
        $capability = new Capability();
        $capability->fromEntity($entity);
        return $capability;
    }

    public function readMultiple(?string $query = null, bool $idAsKey = false): array
    {
        $entities = $this->entityManager->readMultiple(null, Capability::TYPE, $query);

        $capabilities = [];
        foreach ($entities as $entity) {
            $capability = new Capability();
            $capability->fromEntity($entity);
            if ($idAsKey) $capabilities[$capability->id] = $capability;
            else $capabilities[] = $capability;
        }

        return $capabilities;
    }

    public function update(Capability $capability): void
    {
        $entity = $capability->toEntity();
        $this->entityManager->update($entity);
    }

    public function delete(Capability $capability): void
    {
        $entity = $capability->toEntity();
        $this->entityManager->delete($entity);
    }
}
