<?php

namespace API\Managers;

use API\Models\ContextBroker;

class ContextBrokerManager
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create(ContextBroker $contextBroker): void
    {
        $entity = $contextBroker->toEntity();
        $this->entityManager->create($entity);
    }

    public function readOne(string $id): ContextBroker
    {
        $entity = $this->entityManager->readOne($id);
        $contextBroker = new ContextBroker();
        $contextBroker->fromEntity($entity);
        return $contextBroker;
    }

    public function readMultiple(?string $query = null, bool $idAsKey = false): array
    {
        $entities = $this->entityManager->readMultiple(null, ContextBroker::TYPE, $query);

        $contextBrokers = [];
        foreach ($entities as $entity) {
            $contextBroker = new ContextBroker();
            $contextBroker->fromEntity($entity);
            if ($idAsKey) $contextBrokers[$contextBroker->id] = $contextBroker;
            else $contextBrokers[] = $contextBroker;
        }

        return $contextBrokers;
    }

    public function update(ContextBroker $contextBroker): void
    {
        $entity = $contextBroker->toEntity();
        $this->entityManager->update($entity);
    }

    public function delete(ContextBroker $contextBroker): void
    {
        $entity = $contextBroker->toEntity();
        $this->entityManager->delete($entity);
    }
}
