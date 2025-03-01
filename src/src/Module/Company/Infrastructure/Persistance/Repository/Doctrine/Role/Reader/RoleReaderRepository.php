<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\Role\Reader;

use App\Common\Exception\NotFindByUUIDException;
use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
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
            ->createQuery('SELECT r FROM ' . Role::class . ' r WHERE r.' . Role::COLUMN_UUID. ' = :uuid')
            ->setParameter('uuid', $uuid)
            ->getOneOrNullResult();

        if (!$role) {
            throw new NotFindByUUIDException(sprintf('%s : %s', $this->translator->trans('role.uuid.notFound', [], 'roles'), $uuid));
        }

        return $role;
    }

    public function getRoleByName(string $name, ?string $uuid = null): ?Role
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('r')
            ->from(Role::class, 'r')
            ->where('r.' . Role::COLUMN_NAME . ' = :name')
            ->setParameter('name', $name);

        if (null !== $uuid) {
            $qb->andWhere('r.' . Role::COLUMN_UUID . ' != :uuid')
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
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('r')
            ->from(Role::class, 'r')
            ->where('r.' . Role::COLUMN_UUID . ' = :uuid')
            ->setParameter('uuid', $uuid);

        return null !== $qb->getQuery()->getOneOrNullResult();
    }
}
