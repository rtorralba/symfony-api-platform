<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;

abstract class BaseRepository
{

    private ManagerRegistry $managerRegistry;
    protected Connection $connection;
    protected ObjectRepository $objectRepository;

    public function __construct(ManagerRegistry $managerRegistry, Connection $connection)
    {
        $this->managerRegistry = $managerRegistry;
        $this->connection = $connection;
        $this->objectRepository = $this->getEntityManager()->getRepository($this->entityClass());
    }

    abstract protected static function entityClass(): string;

    public function persistEntity(object $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }

    public function flushData(): void
    {
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();
    }

    public function saveEntity(object $entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    public function removeEntity(obfect $entity): void
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    protected function executeFetchQuery(string $query, array $params = []): array
    {
        return $this->connection->executeQuery($query, $params)->fetchAllAssociative();
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    protected function executeQuery(string $query, array $params = [])
    {
        $this->connection->executeQuery($query, $params);
    }

    private function getEntityManager(): \Doctrine\Persistence\ObjectManager
    {
        return $this->managerRegistry->getManager();
    }
}