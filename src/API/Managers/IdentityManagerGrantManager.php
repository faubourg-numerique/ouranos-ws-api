<?php

namespace API\Managers;

use API\Models\IdentityManagerGrant;

class IdentityManagerGrantManager
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create(IdentityManagerGrant $identityManagerGrant): void
    {
        $entity = $identityManagerGrant->toEntity();
        $this->entityManager->create($entity);
    }

    public function readOne(string $id): IdentityManagerGrant
    {
        $entity = $this->entityManager->readOne($id);
        $identityManagerGrant = new IdentityManagerGrant();
        $identityManagerGrant->fromEntity($entity);
        return $identityManagerGrant;
    }

    public function readMultiple(?string $query = null, bool $idAsKey = false): array
    {
        $entities = $this->entityManager->readMultiple(null, IdentityManagerGrant::TYPE, $query);

        $identityManagerGrants = [];
        foreach ($entities as $entity) {
            $identityManagerGrant = new IdentityManagerGrant();
            $identityManagerGrant->fromEntity($entity);
            if ($idAsKey) $identityManagerGrants[$identityManagerGrant->id] = $identityManagerGrant;
            else $identityManagerGrants[] = $identityManagerGrant;
        }

        return $identityManagerGrants;
    }

    public function update(IdentityManagerGrant $identityManagerGrant): void
    {
        $entity = $identityManagerGrant->toEntity();
        $this->entityManager->update($entity);
    }

    public function delete(IdentityManagerGrant $identityManagerGrant): void
    {
        $entity = $identityManagerGrant->toEntity();
        $this->entityManager->delete($entity);
    }
}
