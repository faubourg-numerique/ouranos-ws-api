<?php

namespace API\Managers;

use API\Models\WoTThingDescription;

class WoTThingDescriptionManager
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create(WoTThingDescription $woTThingDescription): void
    {
        $entity = $woTThingDescription->toEntity();
        $this->entityManager->create($entity);
    }

    public function readOne(string $id): WoTThingDescription
    {
        $entity = $this->entityManager->readOne($id);
        $woTThingDescription = new WoTThingDescription();
        $woTThingDescription->fromEntity($entity);
        return $woTThingDescription;
    }

    public function readMultiple(?string $query = null, bool $idAsKey = false): array
    {
        $entities = $this->entityManager->readMultiple(null, WoTThingDescription::TYPE, $query);

        $woTThingDescriptions = [];
        foreach ($entities as $entity) {
            $woTThingDescription = new WoTThingDescription();
            $woTThingDescription->fromEntity($entity);
            if ($idAsKey) $woTThingDescriptions[$woTThingDescription->id] = $woTThingDescription;
            else $woTThingDescriptions[] = $woTThingDescription;
        }

        return $woTThingDescriptions;
    }

    public function update(WoTThingDescription $woTThingDescription): void
    {
        $entity = $woTThingDescription->toEntity();
        $this->entityManager->update($entity);
    }

    public function delete(WoTThingDescription $woTThingDescription): void
    {
        $entity = $woTThingDescription->toEntity();
        $this->entityManager->delete($entity);
    }
}
