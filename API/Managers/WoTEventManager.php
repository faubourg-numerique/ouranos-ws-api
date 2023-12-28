<?php

namespace API\Managers;

use API\Models\WoTEvent;

class WoTEventManager
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create(WoTEvent $woTEvent): void
    {
        $entity = $woTEvent->toEntity();
        $this->entityManager->create($entity);
    }

    public function readOne(string $id): WoTEvent
    {
        $entity = $this->entityManager->readOne($id);
        $woTEvent = new WoTEvent();
        $woTEvent->fromEntity($entity);
        return $woTEvent;
    }

    public function readMultiple(?string $query = null, bool $idAsKey = false): array
    {
        $entities = $this->entityManager->readMultiple(null, WoTEvent::TYPE, $query);

        $woTEvents = [];
        foreach ($entities as $entity) {
            $woTEvent = new WoTEvent();
            $woTEvent->fromEntity($entity);
            if ($idAsKey) $woTEvents[$woTEvent->id] = $woTEvent;
            else $woTEvents[] = $woTEvent;
        }

        return $woTEvents;
    }

    public function update(WoTEvent $woTEvent): void
    {
        $entity = $woTEvent->toEntity();
        $this->entityManager->update($entity);
    }

    public function delete(WoTEvent $woTEvent): void
    {
        $entity = $woTEvent->toEntity();
        $this->entityManager->delete($entity);
    }
}
