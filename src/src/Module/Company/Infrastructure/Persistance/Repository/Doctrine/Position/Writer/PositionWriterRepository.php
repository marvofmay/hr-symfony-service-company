<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\Position\Writer;

use App\Module\Company\Domain\Entity\Position;
use App\Module\Company\Domain\Interface\Position\PositionWriterInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
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

    public function savePositionsInDB(Collection $positions): void
    {
        foreach ($positions as $position) {
            $this->getEntityManager()->persist($position);
        }
        $this->getEntityManager()->flush();
    }

    public function deletePositionInDB(Position $position): void
    {
        if (empty($position)) {
            return;
        }

        $this->getEntityManager()->remove($position);
        $this->getEntityManager()->flush();
    }

    public function deleteMultiplePositionsInDB(Collection $positions): void
    {
        if (empty($positions)) {
            return;
        }

        foreach ($positions as $position) {
            $this->getEntityManager()->remove($position);
        }
        $this->getEntityManager()->flush();
    }
}
