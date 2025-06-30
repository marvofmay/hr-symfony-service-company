<?php

declare(strict_types=1);

namespace App\Common\Infrastructure\Persistance\Repository\Doctrine\EventStore\Writer;

use App\Common\Domain\Entity\EventStore;
use App\Common\Domain\Interface\EventStoreWriterInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class EventStoreWriterRepository extends ServiceEntityRepository implements EventStoreWriterInterface
{
    public function __construct(private readonly ManagerRegistry $registry)
    {
        parent::__construct($registry, EventStore::class);
    }

    public function saveEventStoreInDB(EventStore $eventStore): void
    {
        $this->getEntityManager()->persist($eventStore);
        $this->getEntityManager()->flush();
    }
}
