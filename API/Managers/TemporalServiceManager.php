<?php

namespace API\Managers;

use API\Models\TemporalService;

class TemporalServiceManager
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create(TemporalService\NgsiLd|TemporalService\Mintaka $temporalService): void
    {
        $entity = $temporalService->toEntity();
        $this->entityManager->create($entity);
    }

    public function readOne(string $id): TemporalService\NgsiLd|TemporalService\Mintaka
    {
        $entity = $this->entityManager->readOne($id);
        $temporalService = null;
        if ($entity->getProperty("temporalServiceType") === "ngsi-ld") {
            $temporalService = new TemporalService\NgsiLd();
        } else if ($entity->getProperty("temporalServiceType") === "mintaka") {
            $temporalService = new TemporalService\Mintaka();
        }
        $temporalService->fromEntity($entity);
        return $temporalService;
    }

    public function readMultiple(?string $query = null, bool $idAsKey = false): array
    {
        $entities = $this->entityManager->readMultiple(null, TemporalService::TYPE, $query);

        $temporalServices = [];
        foreach ($entities as $entity) {
            $temporalService = null;
            if ($entity->getProperty("temporalServiceType") === "ngsi-ld") {
                $temporalService = new TemporalService\NgsiLd();
            } else if ($entity->getProperty("temporalServiceType") === "mintaka") {
                $temporalService = new TemporalService\Mintaka();
            }
            $temporalService->fromEntity($entity);
            if ($idAsKey) $temporalServices[$temporalService->id] = $temporalService;
            else $temporalServices[] = $temporalService;
        }

        return $temporalServices;
    }

    public function update(TemporalService\NgsiLd|TemporalService\Mintaka $temporalService): void
    {
        $entity = $temporalService->toEntity();
        $this->entityManager->update($entity);
    }

    public function delete(TemporalService\NgsiLd|TemporalService\Mintaka $temporalService): void
    {
        $entity = $temporalService->toEntity();
        $this->entityManager->delete($entity);
    }
}
