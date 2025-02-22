<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\Position\Writer;

use App\Module\Company\Domain\Entity\Position;
use App\Module\Company\Domain\Interface\Position\PositionWriterInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PositionWriterRepository extends ServiceEntityRepository implements PositionWriterInterface
{
    public function __construct(private readonly ManagerRegistry $registry)
    {
        parent::__construct($registry, Position::class);
    }

    public function savePositionInDB(Position $position): void
    {
        $this->getEntityManager()->persist($position);
        $this->getEntityManager()->flush();
    }

    public function updatePositionInDB(Position $position): void
    {
        $this->getEntityManager()->flush();
    }

    public function savePositionsInDB(array $positions): void
    {
        foreach ($positions as $position) {
            $this->getEntityManager()->persist($position);
        }
        $this->getEntityManager()->flush();
    }

    public function deleteMultiplePositionsInDB(array $selectedUUID): void
    {
        if (empty($selectedUUID)) {
            return;
        }

        $query = $this->getEntityManager()->createQuery('UPDATE App\Module\Company\Domain\Entity\Position p SET p.' . Position::COLUMN_DELETED_AT . ' = :deletedAt WHERE p.' . Position::COLUMN_UUID . ' IN (:uuids)');
        $query->setParameter('deletedAt', (new \DateTime())->format('Y-m-d H:i:s'));
        $query->setParameter('uuids', $selectedUUID);

        $query->execute();
    }
}
