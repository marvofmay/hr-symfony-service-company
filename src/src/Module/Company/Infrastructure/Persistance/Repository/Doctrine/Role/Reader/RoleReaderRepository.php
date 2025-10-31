<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\Role\Reader;

use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Enum\Role\RoleEntityFieldEnum;
use App\Module\Company\Domain\Enum\TimeStampableEntityFieldEnum;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

final class RoleReaderRepository extends ServiceEntityRepository implements RoleReaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Role::class);
    }

    public function getRoleByUUID(string $uuid): ?Role
    {
        return $this->findOneBy([RoleEntityFieldEnum::UUID->value => $uuid]);
    }

    public function getRolesByUUID(array $selectedUUID): Collection
    {
        if (!$selectedUUID) {
            return new ArrayCollection();
        }

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('r')
            ->from(Role::class, 'r')
            ->where('r.'.RoleEntityFieldEnum::UUID->value.' IN (:uuids)')
            ->setParameter('uuids', $selectedUUID);

        $roles = $qb->getQuery()->getResult();

        // $foundUUIDs = array_map(fn (Role $role) => $role->getUUID(), $roles);
        // $missingUUIDs = array_diff($selectedUUID, $foundUUIDs);
        //
        // if ($missingUUIDs) {
        //    throw new NotFindByUUIDException(sprintf('%s : %s', $this->translator->trans('role.uuid.notFound', [], 'roles'), implode(', ', $missingUUIDs)));
        // }

        return new ArrayCollection($roles);
    }

    public function getRoleByName(string $name, ?string $uuid = null): ?Role
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('r')
            ->from(Role::class, 'r')
            ->where('r.'.RoleEntityFieldEnum::NAME->value.' = :name')
            ->setParameter('name', $name);

        if ($uuid) {
            $qb->andWhere('r.'.RoleEntityFieldEnum::UUID->value.' != :uuid')
                ->setParameter('uuid', $uuid);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function isRoleNameAlreadyExists(string $name, ?string $uuid = null): bool
    {
        return !is_null($this->getRoleByName($name, $uuid));
    }

    public function isRoleWithUUIDExists(string $uuid): bool
    {
        return null !== $this->findOneBy([RoleEntityFieldEnum::UUID->value => $uuid]);
    }

    public function getDeletedRoleByUUID(string $uuid): ?Role
    {
        $filters = $this->getEntityManager()->getFilters();
        $filters->disable('soft_delete');

        try {
            return  $this->createQueryBuilder(Role::ALIAS)
                ->where(Role::ALIAS.'.'.RoleEntityFieldEnum::UUID->value.' = :uuid')
                ->andWhere(Role::ALIAS.'.'.TimeStampableEntityFieldEnum::DELETED_AT->value.' IS NOT NULL')
                ->setParameter('uuid', $uuid)
                ->getQuery()
                ->getOneOrNullResult();
        } finally {
            $filters->enable('soft_delete');
        }
    }

    public function getRolesByNames(array $names): Collection
    {
        if (!$names) {
            return new ArrayCollection();
        }

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select(Role::ALIAS)
            ->from(Role::class, Role::ALIAS)
            ->where(Role::ALIAS.'.'.RoleEntityFieldEnum::NAME->value.' IN (:names)')
            ->setParameter('names', $names);

        $roles = $qb->getQuery()->getResult();

        return new ArrayCollection($roles);
    }
}
