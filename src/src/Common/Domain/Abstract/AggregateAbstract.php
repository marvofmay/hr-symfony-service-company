<?php

declare(strict_types=1);

namespace App\Common\Domain\Abstract;

use App\Common\Domain\Entity\EventStore;
use App\Common\Domain\Service\EventStore\EventStoreCreator;
use App\Module\Company\Domain\Interface\Company\CompanyAggregateReaderInterface;
use App\Module\Company\Domain\Interface\Department\DepartmentAggregateReaderInterface;
use App\Module\Company\Domain\Interface\Employee\EmployeeAggregateReaderInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Serializer\SerializerInterface;

class AggregateAbstract
{
    public function __construct(
        protected EventStoreCreator $eventStoreCreator,
        protected Security $security,
        protected SerializerInterface $serializer,
        protected EventDispatcherInterface $eventDispatcher,
        protected CompanyAggregateReaderInterface $companyAggregateReaderRepository,
        protected DepartmentAggregateReaderInterface $departmentAggregateReaderRepository,
        protected EmployeeAggregateReaderInterface $employeeAggregateReaderRepository,
    ) {
    }

    protected function commitEvents(array $events, string $aggregateClass): void
    {
        foreach ($events as $event) {
            $this->eventStoreCreator->create(
                new EventStore(
                    $event->uuid->toString(),
                    $event::class,
                    $aggregateClass,
                    $this->serializer->serialize($event, 'json'),
                    $this->security->getUser(),
                )
            );

            $this->eventDispatcher->dispatch($event);
        }
    }
}
