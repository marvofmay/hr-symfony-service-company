<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\PositionDepartment\Reader;

use App\Module\Company\Domain\Entity\PositionDepartment;
use App\Module\Company\Domain\Interface\PositionDepartment\PositionDepartmentReaderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

final class PositionDepartmentReaderRepository extends ServiceEntityRepository implements PositionDepartmentReaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PositionDepartment::class);
    }

    public function getDeletedPositionDepartmentsByPositionUUID(string $positionUUID): Collection
    {
        $filters = $this->getEntityManager()->getFilters();
        $filters->disable('soft_delete');

        try {
            $qb = $this->getEntityManager()->createQueryBuilder();
            $positionDepartments = $qb->select('pd')
                ->from(PositionDepartment::class, 'pd')
                ->join('pd.position', 'p')
                ->where('p.uuid = :uuid')
                ->andWhere('pd.deletedAt IS NOT NULL')
                ->setParameter('uuid', $positionUUID)
                ->getQuery()
                ->getResult();

            return new ArrayCollection($positionDepartments);
        } finally {
            $filters->enable('soft_delete');
        }
    }
}
