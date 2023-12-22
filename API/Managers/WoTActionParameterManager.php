<?php

namespace API\Managers;

use API\Models\WoTActionParameter;

class WoTActionParameterManager
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create(WoTActionParameter $woTActionParameter): void
    {
        $entity = $woTActionParameter->toEntity();
        $this->entityManager->create($entity);
    }

    public function readOne(string $id): WoTActionParameter
    {
        $entity = $this->entityManager->readOne($id);
        $woTActionParameter = new WoTActionParameter();
        $woTActionParameter->fromEntity($entity);
        return $woTActionParameter;
    }

    public function readMultiple(?string $query = null, bool $idAsKey = false): array
    {
        $entities = $this->entityManager->readMultiple(null, WoTActionParameter::TYPE, $query);

        $woTActionParameters = [];
        foreach ($entities as $entity) {
            $woTActionParameter = new WoTActionParameter();
            $woTActionParameter->fromEntity($entity);
            if ($idAsKey) $woTActionParameters[$woTActionParameter->id] = $woTActionParameter;
            else $woTActionParameters[] = $woTActionParameter;
        }

        return $woTActionParameters;
    }

    public function update(WoTActionParameter $woTActionParameter): void
    {
        $entity = $woTActionParameter->toEntity();
        $this->entityManager->update($entity);
    }

    public function delete(WoTActionParameter $woTActionParameter): void
    {
        $entity = $woTActionParameter->toEntity();
        $this->entityManager->delete($entity);
    }
}
