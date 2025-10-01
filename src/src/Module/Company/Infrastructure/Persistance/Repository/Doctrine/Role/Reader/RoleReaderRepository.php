<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\Role\Reader;

use App\Common\Domain\Exception\NotFindByUUIDException;
use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

final class RoleReaderRepository extends ServiceEntityRepository implements RoleReaderInterface
{
    public function __construct(ManagerRegistry $registry, private readonly TranslatorInterface $translator)
    {
        parent::__construct($registry, Role::class);
    }

    public function getRoleByUUID(string $uuid): ?Role
    {
        $role = $this->findOneBy([Role::COLUMN_UUID => $uuid]);
        if (null === $role) {
            throw new \Exception($this->translator->trans('role.uuid.notExists', [':uuid' => $uuid], 'roles'), Response::HTTP_NOT_FOUND);
        }

        return $role;
    }

    public function getRolesByUUID(array $selectedUUID): Collection
    {
        if (!$selectedUUID) {
            return new ArrayCollection();
        }

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('r')
            ->from(Role::class, 'r')
            ->where('r.'.Role::COLUMN_UUID.' IN (:uuids)')
            ->setParameter('uuids', $selectedUUID);

        $roles = $qb->getQuery()->getResult();

        //$foundUUIDs = array_map(fn (Role $role) => $role->getUUID(), $roles);
        //$missingUUIDs = array_diff($selectedUUID, $foundUUIDs);
        //
        //if ($missingUUIDs) {
        //    throw new NotFindByUUIDException(sprintf('%s : %s', $this->translator->trans('role.uuid.notFound', [], 'roles'), implode(', ', $missingUUIDs)));
        //}

        return new ArrayCollection($roles);
    }

    public function getRoleByName(string $name, ?string $uuid = null): ?Role
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('r')
            ->from(Role::class, 'r')
            ->where('r.'.Role::COLUMN_NAME.' = :name')
            ->setParameter('name', $name);

        if ($uuid) {
            $qb->andWhere('r.'.Role::COLUMN_UUID.' != :uuid')
                ->setParameter('uuid', $uuid);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function isRoleExists(string $name, ?string $uuid = null): bool
    {
        return !is_null($this->getRoleByName($name, $uuid));
    }

    public function isRoleWithUUIDExists(string $uuid): bool
    {
        return null !== $this->findOneBy([Role::COLUMN_UUID => $uuid]);
    }
}
