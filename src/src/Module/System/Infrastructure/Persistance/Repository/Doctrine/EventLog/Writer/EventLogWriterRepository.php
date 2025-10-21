<?php

declare(strict_types=1);

namespace App\Module\System\Infrastructure\Persistance\Repository\Doctrine\EventLog\Writer;

use App\Module\System\Domain\Entity\EventLog;
use App\Module\System\Domain\Interface\EventLog\EventLogWriterInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EventLogWriterRepository extends ServiceEntityRepository implements EventLogWriterInterface
{
    public function __construct(private readonly ManagerRegistry $registry)
    {
        parent::__construct($registry, EventLog::class);
    }

    public function saveEventLogInDB(EventLog $eventLog): void
    {
        $this->getEntityManager()->persist($eventLog);
        $this->getEntityManager()->flush();
    }
}
