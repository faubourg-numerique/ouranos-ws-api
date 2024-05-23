<?php

namespace API\Managers;

use API\Models\RoutingOperationControl;

class RoutingOperationControlManager
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create(RoutingOperationControl $routingOperationControl): void
    {
        $entity = $routingOperationControl->toEntity();
        $this->entityManager->create($entity);
    }

    public function readOne(string $id): RoutingOperationControl
    {
        $entity = $this->entityManager->readOne($id);
        $routingOperationControl = new RoutingOperationControl();
        $routingOperationControl->fromEntity($entity);
        return $routingOperationControl;
    }

    public function readMultiple(?string $query = null, bool $idAsKey = false): array
    {
        $entities = $this->entityManager->readMultiple(null, RoutingOperationControl::TYPE, $query);

        $routingOperationControls = [];
        foreach ($entities as $entity) {
            $routingOperationControl = new RoutingOperationControl();
            $routingOperationControl->fromEntity($entity);
            if ($idAsKey) $routingOperationControls[$routingOperationControl->id] = $routingOperationControl;
            else $routingOperationControls[] = $routingOperationControl;
        }

        return $routingOperationControls;
    }

    public function update(RoutingOperationControl $routingOperationControl): void
    {
        $entity = $routingOperationControl->toEntity();
        $this->entityManager->update($entity);
    }

    public function delete(RoutingOperationControl $routingOperationControl): void
    {
        $entity = $routingOperationControl->toEntity();
        $this->entityManager->delete($entity);
    }
}
