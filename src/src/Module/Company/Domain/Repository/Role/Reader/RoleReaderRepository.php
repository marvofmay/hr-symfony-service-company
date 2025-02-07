<?php

declare(strict_types = 1);

namespace App\module\company\Domain\Repository\Role\Reader;

use App\module\company\Domain\Entity\Role;
use App\module\company\Domain\Exception\NotFindByUUIDException;
use App\module\company\Domain\Interface\Role\RoleReaderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Translation\TranslatorInterface;

class RoleReaderRepository extends ServiceEntityRepository implements RoleReaderInterface
{
    public function __construct(ManagerRegistry $registry, private readonly TranslatorInterface $translator)
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
            throw new NotFindByUUIDException(sprintf('%s : %s', $this->translator->trans('role.uuid.notFound'), $uuid));
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

    public function isRoleExists(string $name): bool
    {
        return !is_null($this->getRoleByName($name));
    }
}