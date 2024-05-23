<?php

namespace API\Managers;

use API\Models\RoutingOperation;

class RoutingOperationManager
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create(RoutingOperation $routingOperation): void
    {
        $entity = $routingOperation->toEntity();
        $this->entityManager->create($entity);
    }

    public function readOne(string $id): RoutingOperation
    {
        $entity = $this->entityManager->readOne($id);
        $routingOperation = new RoutingOperation();
        $routingOperation->fromEntity($entity);
        return $routingOperation;
    }

    public function readMultiple(?string $query = null, bool $idAsKey = false): array
    {
        $entities = $this->entityManager->readMultiple(null, RoutingOperation::TYPE, $query);

        $routingOperations = [];
        foreach ($entities as $entity) {
            $routingOperation = new RoutingOperation();
            $routingOperation->fromEntity($entity);
            if ($idAsKey) $routingOperations[$routingOperation->id] = $routingOperation;
            else $routingOperations[] = $routingOperation;
        }

        return $routingOperations;
    }

    public function update(RoutingOperation $routingOperation): void
    {
        $entity = $routingOperation->toEntity();
        $this->entityManager->update($entity);
    }

    public function delete(RoutingOperation $routingOperation): void
    {
        $entity = $routingOperation->toEntity();
        $this->entityManager->delete($entity);
    }
}
