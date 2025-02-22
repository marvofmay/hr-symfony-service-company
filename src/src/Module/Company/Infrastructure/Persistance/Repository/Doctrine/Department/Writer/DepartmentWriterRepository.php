<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\Department\Writer;

use App\Module\Company\Domain\Entity\Department;
use App\Module\Company\Domain\Interface\Department\DepartmentWriterInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DepartmentWriterRepository extends ServiceEntityRepository implements DepartmentWriterInterface
{
    public function __construct(private readonly ManagerRegistry $registry)
    {
        parent::__construct($registry, Department::class);
    }

    public function saveDepartmentInDB(Department $department): void
    {
        $this->getEntityManager()->persist($department);
        $this->getEntityManager()->flush();
    }

    public function updateDepartmentInDB(Department $department): void
    {
        $this->getEntityManager()->flush();
    }

    public function saveDepartmentsInDB(array $departments): void
    {
        foreach ($departments as $department) {
            $this->getEntityManager()->persist($department);
        }
        $this->getEntityManager()->flush();
    }

    public function deleteMultipleDepartmentsInDB(array $selectedUUID): void
    {
        if (empty($selectedUUID)) {
            return;
        }

        $query = $this->getEntityManager()->createQuery('UPDATE App\Module\Company\Domain\Entity\Department d SET d. ' . Department::COLUMN_DELETED_AT . ' = :deletedAt WHERE d.' . Department::COLUMN_UUID . ' IN (:uuids)');
        $query->setParameter('deletedAt', (new \DateTime())->format('Y-m-d H:i:s'));
        $query->setParameter('uuids', $selectedUUID);

        $query->execute();
    }
}
