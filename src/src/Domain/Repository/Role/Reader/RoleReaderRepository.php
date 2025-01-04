<?php

declare(strict_types = 1);

namespace App\Domain\Repository\Role\Reader;

use App\Domain\Entity\Role;
use App\Domain\Exception\NotFindByUUIDException;
use App\Domain\Interface\Role\RoleReaderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RoleReaderRepository extends ServiceEntityRepository implements RoleReaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Role::class);
    }
    public function getRoleByUUID(string $uuid): ?Role
    {
        $role = $this->getEntityManager()
            ->createQuery('SELECT r FROM App\Domain\Entity\Role r WHERE r.uuid = :uuid')
            ->setParameter('uuid', $uuid)
            ->getOneOrNullResult();

        if (!$role) {
            throw new NotFindByUUIDException('Role not found by uuid: ' . $uuid);
        }

        return $role;
    }

    public function getRoleByName(string $name): ?Role
    {
        $role = $this->getEntityManager()
            ->createQuery('SELECT r FROM App\Domain\Entity\Role r WHERE r.name = :name')
            ->setParameter('name', $name)
            ->getOneOrNullResult();

        return $role;
    }
}