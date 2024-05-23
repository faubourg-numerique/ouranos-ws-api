<?php

namespace API\Managers;

use API\Models\Service;

class ServiceManager
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create(Service $service): void
    {
        $entity = $service->toEntity();
        $this->entityManager->create($entity);
    }

    public function readOne(string $id): Service
    {
        $entity = $this->entityManager->readOne($id);
        $service = new Service();
        $service->fromEntity($entity);
        return $service;
    }

    public function readMultiple(?string $query = null, bool $idAsKey = false): array
    {
        $entities = $this->entityManager->readMultiple(null, Service::TYPE, $query);

        $services = [];
        foreach ($entities as $entity) {
            $service = new Service();
            $service->fromEntity($entity);
            if ($idAsKey) $services[$service->id] = $service;
            else $services[] = $service;
        }

        return $services;
    }

    public function update(Service $service): void
    {
        $entity = $service->toEntity();
        $this->entityManager->update($entity);
    }

    public function delete(Service $service): void
    {
        $entity = $service->toEntity();
        $this->entityManager->delete($entity);
    }
}
