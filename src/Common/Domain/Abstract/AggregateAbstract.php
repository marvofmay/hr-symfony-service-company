<?php

declare(strict_types=1);

namespace App\Common\Domain\Abstract;

use App\Common\Domain\Entity\EventStore;
use App\Common\Domain\Service\EventStore\EventStoreCreator;
use App\Module\Company\Domain\Interface\Company\CompanyAggregateReaderInterface;
use App\Module\Company\Domain\Interface\Department\DepartmentAggregateReaderInterface;
use App\Module\Company\Domain\Interface\Employee\EmployeeAggregateReaderInterface;
use App\Module\Company\Domain\Interface\User\UserReaderInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Serializer\SerializerInterface;

class AggregateAbstract
{
    public function __construct(
        protected EventStoreCreator $eventStoreCreator,
        protected SerializerInterface $serializer,
        protected EventDispatcherInterface $eventDispatcher,
        protected CompanyAggregateReaderInterface $companyAggregateReaderRepository,
        protected DepartmentAggregateReaderInterface $departmentAggregateReaderRepository,
        protected EmployeeAggregateReaderInterface $employeeAggregateReaderRepository,
        protected UserReaderInterface $userReaderReaderRepository,
    ) {
    }

    protected function commitEvents(array $events, string $aggregateClass): void
    {
        $loggedUser = null;
        foreach ($events as $event) {
            if (null === $loggedUser) {
                $loggedUser = $this->userReaderReaderRepository->getUserByUUID($event->loggedUserUUID->toString());
            }
            $this->eventStoreCreator->create(
                new EventStore(
                    $event->uuid->toString(),
                    $event::class,
                    $aggregateClass,
                    $this->serializer->serialize($event, 'json'),
                    $loggedUser,
                )
            );

            $this->eventDispatcher->dispatch($event);
        }
    }
}
