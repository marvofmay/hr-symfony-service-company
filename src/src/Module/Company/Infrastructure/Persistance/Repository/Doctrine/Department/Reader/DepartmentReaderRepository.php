<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\Department\Reader;

use App\Common\Domain\Exception\NotFindByUUIDException;
use App\Module\Company\Domain\Entity\Address;
use App\Module\Company\Domain\Entity\Contact;
use App\Module\Company\Domain\Entity\Department;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

final class DepartmentReaderRepository extends ServiceEntityRepository implements DepartmentReaderInterface
{
    public function __construct(ManagerRegistry $registry, private readonly TranslatorInterface $translator)
    {
        parent::__construct($registry, Department::class);
    }

    public function getDepartmentByUUID(string $uuid): Department
    {
        $department = $this->findOneBy([Department::COLUMN_UUID => $uuid]);
        if (null === $department) {
            throw new \Exception($this->translator->trans('department.uuid.notExists', [':uuid' => $uuid], 'departments'), Response::HTTP_NOT_FOUND);
        }

        return $department;
    }

    public function getDepartmentsByUUID(array $selectedUUID): Collection
    {
        if (!$selectedUUID) {
            return new ArrayCollection();
        }

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select(Department::ALIAS)
            ->from(Department::class, Department::ALIAS)
            ->where(Department::ALIAS.'.'.Department::COLUMN_UUID.' IN (:uuids)')
            ->setParameter('uuids', $selectedUUID);

        $departments = $qb->getQuery()->getResult();

        // $foundUUIDs = array_map(fn (Department $department) => $department->getUUID(), $departments);
        // $missingUUIDs = array_diff($selectedUUID, $foundUUIDs);
        //
        // if ($missingUUIDs) {
        //    throw new NotFindByUUIDException(sprintf('%s : %s', $this->translator->trans('department.uuid.notFound', [], 'departments'), implode(', ', $missingUUIDs)));
        // }

        return new ArrayCollection($departments);
    }

    public function getDepartmentByName(string $name, ?string $uuid = null): ?Department
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('d')
            ->from(Department::class, 'd')
            ->where('d.'.Department::COLUMN_NAME.' = :name')
            ->setParameter('name', $name);

        if (null !== $uuid) {
            $qb->andWhere('d.'.Department::COLUMN_UUID.' != :uuid')
                ->setParameter('uuid', $uuid);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function isDepartmentExistsWithName(string $name, ?string $departmentUUID = null): bool
    {
        return !is_null($this->getDepartmentByName($name, $departmentUUID));
    }

    public function getDepartmentByInternalCode(string $internalCode, ?string $uuid = null): ?Department
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select(Department::ALIAS)
            ->from(Department::class, Department::ALIAS)
            ->where(Department::ALIAS.'.'.Department::COLUMN_INTERNAL_CODE.' = :internalCode')
            ->setParameter('internalCode', $internalCode);

        if (null !== $uuid) {
            $qb->andWhere(Department::ALIAS.'.'.Department::COLUMN_UUID.' != :uuid')
                ->setParameter('uuid', $uuid);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function isDepartmentExistsWithUUID(string $departmentUUID): bool
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('d')
            ->from(Department::class, 'd')
            ->where('d.'.Department::COLUMN_UUID.' = :uuid')
            ->setParameter('uuid', $departmentUUID);

        return null !== $qb->getQuery()->getOneOrNullResult();
    }

    public function isDepartmentExistsWithInternalCode(string $internalCode, ?string $uuid = null): bool
    {
        return !is_null($this->getDepartmentByInternalCode($internalCode, $uuid));
    }

    public function getDeletedDepartmentByUUID(string $uuid): ?Department
    {
        $filters = $this->getEntityManager()->getFilters();
        $filters->disable('soft_delete');

        try {
            $deletedDepartment = $this->createQueryBuilder(Department::ALIAS)
                ->where(Department::ALIAS.'.'.Department::COLUMN_UUID.' = :uuid')
                ->andWhere(Department::ALIAS.'.'.Department::COLUMN_DELETED_AT.' IS NOT NULL')
                ->setParameter('uuid', $uuid)
                ->getQuery()
                ->getOneOrNullResult();

            if (null === $deletedDepartment) {
                throw new \Exception($this->translator->trans('department.deleted.notExists', [':uuid' => $uuid], 'departments'), Response::HTTP_NOT_FOUND);
            }

            return $deletedDepartment;
        } finally {
            $filters->enable('soft_delete');
        }
    }

    public function getDeletedAddressByDepartmentByUUID(string $uuid): ?Address
    {
        $filters = $this->getEntityManager()->getFilters();
        $filters->disable('soft_delete');

        try {
            $qb = $this->getEntityManager()->createQueryBuilder();
            $deletedAddress = $qb->select(Address::ALIAS)
                ->from(Address::class, Address::ALIAS)
                ->join(Address::ALIAS.'.department', Department::ALIAS)
                ->where(Department::ALIAS.'.uuid = :uuid')
                ->andWhere(Address::ALIAS.'.deletedAt IS NOT NULL')
                ->setParameter('uuid', $uuid)
                ->getQuery()
                ->getOneOrNullResult();

            if (null === $deletedAddress) {
                throw new \Exception($this->translator->trans('department.deleted.address.notExists', [':uuid' => $uuid], 'departments'), Response::HTTP_NOT_FOUND);
            }

            return $deletedAddress;
        } finally {
            $filters->enable('soft_delete');
        }
    }

    public function getDeletedContactsByDepartmentByUUID(string $uuid): Collection
    {
        $filters = $this->getEntityManager()->getFilters();
        $filters->disable('soft_delete');

        try {
            $qb = $this->getEntityManager()->createQueryBuilder();
            $deletedContacts = $qb->select(Contact::ALIAS)
                ->from(Contact::class, Contact::ALIAS)
                ->join(Contact::ALIAS.'.'.Contact::RELATION_DEPARTMENT, 'co')
                ->where('co.uuid = :uuid')
                ->andWhere(Contact::ALIAS.'.deletedAt IS NOT NULL')
                ->setParameter('uuid', $uuid)
                ->getQuery()
                ->getResult();

            if (empty($deletedContacts)) {
                throw new \Exception($this->translator->trans('department.deleted.contacts.notExists', [':uuid' => $uuid], 'departments'), Response::HTTP_NOT_FOUND);
            }

            return new ArrayCollection($deletedContacts);
        } finally {
            $filters->enable('soft_delete');
        }
    }
}
