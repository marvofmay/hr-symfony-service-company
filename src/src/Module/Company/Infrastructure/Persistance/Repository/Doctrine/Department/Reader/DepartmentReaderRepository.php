<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\Department\Reader;

use App\Module\Company\Domain\Entity\Department;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

class DepartmentReaderRepository extends ServiceEntityRepository implements DepartmentReaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Department::class);
    }

    public function getDepartmentByUUID(string $uuid): ?Department
    {
        return $this->getEntityManager()
            ->createQuery('SELECT d FROM ' . Department::class . ' d WHERE d.' . Department::COLUMN_UUID . ' = :uuid')
            ->setParameter('uuid', $uuid)
            ->getOneOrNullResult();
    }

    public function getDepartmentsByUUID(array $selectedUUID): Collection
    {
        $departments = $this->getEntityManager()
            ->createQuery(
                'SELECT d FROM ' . Department::class . ' d WHERE d.' . Department::COLUMN_UUID . ' IN (:uuid)'
            )
            ->setParameter('uuid', $selectedUUID)
            ->getResult();

        return new ArrayCollection($departments);
    }

    public function getDepartmentByName(string $name, ?string $uuid = null): ?Department
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('d')
            ->from(Department::class, 'd')
            ->where('d.' . Department::COLUMN_NAME . ' = :name')
            ->setParameter('name', $name);

        if (null !== $uuid) {
            $qb->andWhere('d.' . Department::COLUMN_UUID . ' != :uuid')
                ->setParameter('uuid', $uuid);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function isDepartmentExistsWithName(string $name, ?string $departmentUUID = null): bool
    {
        return !is_null($this->getDepartmentByName($name, $departmentUUID));
    }

    public function isDepartmentExistsWithUUID(string $departmentUUID): bool
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('d')
            ->from(Department::class, 'd')
            ->where('d.' . Department::COLUMN_UUID . ' = :uuid')
            ->setParameter('uuid', $departmentUUID);

        return null !== $qb->getQuery()->getOneOrNullResult();
    }
}
