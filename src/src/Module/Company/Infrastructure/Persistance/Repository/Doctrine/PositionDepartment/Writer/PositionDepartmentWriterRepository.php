<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\PositionDepartment\Writer;

use App\Common\Domain\Enum\DeleteTypeEnum;
use App\Module\Company\Domain\Entity\Department;
use App\Module\Company\Domain\Entity\Position;
use App\Module\Company\Domain\Entity\PositionDepartment;
use App\Module\Company\Domain\Interface\PositionDepartment\PositionDepartmentWriterInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class PositionDepartmentWriterRepository extends ServiceEntityRepository implements PositionDepartmentWriterInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PositionDepartment::class);
    }

    public function deletePositionDepartmentsByPositionInDB(Position $position, DeleteTypeEnum $deleteTypeEnum = DeleteTypeEnum::SOFT_DELETE): void
    {
        if (DeleteTypeEnum::HARD_DELETE === $deleteTypeEnum) {
            $this->getEntityManager()->createQueryBuilder()
                ->delete(PositionDepartment::class, 'pd')
                ->where('pd.position = :uuid')
                ->setParameter('uuid', $position->uuid)
                ->getQuery()
                ->execute();
        } else {
            $positionDepartments = $this->findBy(['position' => $position]);

            foreach ($positionDepartments as $pd) {
                $this->getEntityManager()->remove($pd);
            }

            $this->getEntityManager()->flush();
        }
    }

    public function deletePositionDepartmentByPositionInDB(Position $position, Department $department, DeleteTypeEnum $deleteTypeEnum = DeleteTypeEnum::SOFT_DELETE): void
    {
        if (DeleteTypeEnum::HARD_DELETE === $deleteTypeEnum) {
            $this->getEntityManager()->createQueryBuilder()
                ->delete(PositionDepartment::class, 'pd')
                ->where('pd.position = :positionUUID')
                ->andWhere('pd.department = :departmentUUID')
                ->setParameter('positionUUID', $position->uuid)
                ->setParameter('departmentUUID', $department->getUUID())
                ->getQuery()
                ->execute();
        } else {
            $positionDepartments = $this->findBy(['position' => $position]);

            foreach ($positionDepartments as $pd) {
                $this->getEntityManager()->remove($pd);
            }

            $this->getEntityManager()->flush();
        }
    }
}
