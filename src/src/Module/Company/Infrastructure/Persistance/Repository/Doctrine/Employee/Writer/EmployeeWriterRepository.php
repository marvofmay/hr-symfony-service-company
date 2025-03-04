<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\Employee\Writer;

use App\Module\Company\Domain\Entity\Employee;
use App\Module\Company\Domain\Interface\Employee\EmployeeWriterInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EmployeeWriterRepository extends ServiceEntityRepository implements EmployeeWriterInterface
{
    public function __construct(private readonly ManagerRegistry $registry)
    {
        parent::__construct($registry, Employee::class);
    }

    public function saveEmployeeInDB(Employee $employee): void
    {
        $this->getEntityManager()->persist($employee);
        $this->getEntityManager()->flush();
    }

    public function updateEmployeeInDB(Employee $employee): void
    {
        $this->getEntityManager()->persist($employee);
        $this->getEntityManager()->flush();
    }

    public function saveEmployeesInDB(array $employees): void
    {
        foreach ($employees as $employee) {
            $this->getEntityManager()->persist($employee);
        }
        $this->getEntityManager()->flush();
    }

    public function deleteMultipleEmployeesInDB(array $selectedUUID): void
    {
        if (empty($selectedUUID)) {
            return;
        }

        $query = $this->getEntityManager()->createQuery('UPDATE ' . Employee::class . ' e SET e. ' . Employee::COLUMN_DELETED_AT . ' = :deletedAt WHERE e.' . Employee::COLUMN_UUID . ' IN (:uuids)');
        $query->setParameter('deletedAt', (new \DateTime())->format('Y-m-d H:i:s'));
        $query->setParameter('uuids', $selectedUUID);

        $query->execute();
    }
}
