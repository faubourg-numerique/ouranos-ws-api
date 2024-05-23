<?php

namespace API\Managers;

use API\Models\Routing;

class RoutingManager
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create(Routing $routing): void
    {
        $entity = $routing->toEntity();
        $this->entityManager->create($entity);
    }

    public function readOne(string $id): Routing
    {
        $entity = $this->entityManager->readOne($id);
        $routing = new Routing();
        $routing->fromEntity($entity);
        return $routing;
    }

    public function readMultiple(?string $query = null, bool $idAsKey = false): array
    {
        $entities = $this->entityManager->readMultiple(null, Routing::TYPE, $query);

        $routings = [];
        foreach ($entities as $entity) {
            $routing = new Routing();
            $routing->fromEntity($entity);
            if ($idAsKey) $routings[$routing->id] = $routing;
            else $routings[] = $routing;
        }

        return $routings;
    }

    public function update(Routing $routing): void
    {
        $entity = $routing->toEntity();
        $this->entityManager->update($entity);
    }

    public function delete(Routing $routing): void
    {
        $entity = $routing->toEntity();
        $this->entityManager->delete($entity);
    }
}
