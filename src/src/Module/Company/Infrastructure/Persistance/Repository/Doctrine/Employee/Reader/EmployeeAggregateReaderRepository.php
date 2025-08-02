<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\Employee\Reader;

use App\Common\Domain\Entity\EventStore;
use App\Module\Company\Domain\Aggregate\Employee\EmployeeAggregate;
use App\Module\Company\Domain\Aggregate\Employee\ValueObject\EmployeeUUID;
use App\Module\Company\Domain\Interface\Employee\EmployeeAggregateReaderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\SerializerInterface;

final class EmployeeAggregateReaderRepository extends ServiceEntityRepository implements EmployeeAggregateReaderInterface
{
    public function __construct(ManagerRegistry $registry, private SerializerInterface $serializer)
    {
        parent::__construct($registry, EventStore::class);
    }

    public function getEmployeeAggregateByUUID(EmployeeUUID $uuid): EmployeeAggregate
    {
        $events = $this->findBy([
            'aggregateUUID'  => $uuid->toString(),
            'aggregateClass' => EmployeeAggregate::class,
        ], ['createdAt' => 'ASC']);

        if (empty($events)) {
            throw new \RuntimeException('Aggregate not found: ' . $uuid->toString());
        }

        $domainEvents = [];

        foreach ($events as $eventEntity) {
            $payload = json_decode($eventEntity->getPayload(), true);
            $event = $this->serializer->deserialize(
                $eventEntity->getPayload(),
                $eventEntity->getAggregateType(),
                'json',
                [
                    'employmentFrom' => $payload['employmentFrom'] ?? null,
                ]
            );

            $domainEvents[] = $event;
        }

        return EmployeeAggregate::reconstituteFromHistory($domainEvents);
    }
}
