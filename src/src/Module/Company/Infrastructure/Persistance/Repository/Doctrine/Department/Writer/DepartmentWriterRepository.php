<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\Department\Writer;

use App\Module\Company\Domain\Entity\Department;
use App\Module\Company\Domain\Interface\Department\DepartmentWriterInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

final class DepartmentWriterRepository extends ServiceEntityRepository implements DepartmentWriterInterface
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

    public function saveDepartmentsInDB(Collection $departments): void
    {
        foreach ($departments as $department) {
            $this->getEntityManager()->persist($department);
        }
        $this->getEntityManager()->flush();
    }

    public function deleteDepartmentInDB(Department $department): void
    {
        $this->getEntityManager()->remove($department);
        $this->getEntityManager()->flush();
    }

    public function deleteMultipleDepartmentsInDB(Collection $departments): void
    {
        if (empty($departments)) {
            return;
        }

        foreach ($departments as $department) {
            $this->getEntityManager()->remove($department);
        }

        $this->getEntityManager()->flush();
    }
}
