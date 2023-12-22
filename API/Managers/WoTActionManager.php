<?php

namespace API\Managers;

use API\Models\WoTAction;

class WoTActionManager
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create(WoTAction $woTAction): void
    {
        $entity = $woTAction->toEntity();
        $this->entityManager->create($entity);
    }

    public function readOne(string $id): WoTAction
    {
        $entity = $this->entityManager->readOne($id);
        $woTAction = new WoTAction();
        $woTAction->fromEntity($entity);
        return $woTAction;
    }

    public function readMultiple(?string $query = null, bool $idAsKey = false): array
    {
        $entities = $this->entityManager->readMultiple(null, WoTAction::TYPE, $query);

        $woTActions = [];
        foreach ($entities as $entity) {
            $woTAction = new WoTAction();
            $woTAction->fromEntity($entity);
            if ($idAsKey) $woTActions[$woTAction->id] = $woTAction;
            else $woTActions[] = $woTAction;
        }

        return $woTActions;
    }

    public function update(WoTAction $woTAction): void
    {
        $entity = $woTAction->toEntity();
        $this->entityManager->update($entity);
    }

    public function delete(WoTAction $woTAction): void
    {
        $entity = $woTAction->toEntity();
        $this->entityManager->delete($entity);
    }
}
