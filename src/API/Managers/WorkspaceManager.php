<?php

namespace API\Managers;

use API\Models\Workspace;

class WorkspaceManager
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create(Workspace $workspace): void
    {
        $entity = $workspace->toEntity();
        $this->entityManager->create($entity);
    }

    public function readOne(string $id): Workspace
    {
        $entity = $this->entityManager->readOne($id);
        $workspace = new Workspace();
        $workspace->fromEntity($entity);
        return $workspace;
    }

    public function readMultiple(?string $query = null, bool $idAsKey = false): array
    {
        $entities = $this->entityManager->readMultiple(null, Workspace::TYPE, $query);

        $workspaces = [];
        foreach ($entities as $entity) {
            $workspace = new Workspace();
            $workspace->fromEntity($entity);
            if ($idAsKey) $workspaces[$workspace->id] = $workspace;
            else $workspaces[] = $workspace;
        }

        return $workspaces;
    }

    public function update(Workspace $workspace): void
    {
        $entity = $workspace->toEntity();
        $this->entityManager->update($entity);
    }

    public function delete(Workspace $workspace): void
    {
        $entity = $workspace->toEntity();
        $this->entityManager->delete($entity);
    }
}
