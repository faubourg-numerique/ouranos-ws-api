<?php

namespace API\Managers;

use API\Models\IdentityManager;

class IdentityManagerManager
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create(IdentityManager $identityManager): void
    {
        $entity = $identityManager->toEntity();
        $this->entityManager->create($entity);
    }

    public function readOne(string $id): IdentityManager
    {
        $entity = $this->entityManager->readOne($id);
        $identityManager = new IdentityManager();
        $identityManager->fromEntity($entity);
        return $identityManager;
    }

    public function readMultiple(?string $query = null, bool $idAsKey = false): array
    {
        $entities = $this->entityManager->readMultiple(null, IdentityManager::TYPE, $query);

        $identityManagers = [];
        foreach ($entities as $entity) {
            $identityManager = new IdentityManager();
            $identityManager->fromEntity($entity);
            if ($idAsKey) $identityManagers[$identityManager->id] = $identityManager;
            else $identityManagers[] = $identityManager;
        }

        return $identityManagers;
    }

    public function update(IdentityManager $identityManager): void
    {
        $entity = $identityManager->toEntity();
        $this->entityManager->update($entity);
    }

    public function delete(IdentityManager $identityManager): void
    {
        $entity = $identityManager->toEntity();
        $this->entityManager->delete($entity);
    }
}
