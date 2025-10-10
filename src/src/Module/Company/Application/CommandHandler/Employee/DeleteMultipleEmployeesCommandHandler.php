<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Employee;

use App\Common\Domain\Entity\EventStore;
use App\Common\Domain\Service\EventStore\EventStoreCreator;
use App\Module\Company\Application\Command\Employee\DeleteMultipleEmployeesCommand;
use App\Module\Company\Domain\Aggregate\Employee\EmployeeAggregate;
use App\Module\Company\Domain\Aggregate\Employee\ValueObject\EmployeeUUID;
use App\Module\Company\Domain\Event\Employee\EmployeeMultipleDeletedEvent;
use App\Module\Company\Domain\Interface\Employee\EmployeeAggregateReaderInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class DeleteMultipleEmployeesCommandHandler
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private EmployeeAggregateReaderInterface $employeeAggregateReaderRepository,
        private EventStoreCreator $eventStoreCreator,
        private Security $security,
        private SerializerInterface $serializer,
    ) {
    }

    public function __invoke(DeleteMultipleEmployeesCommand $command): void
    {
        $deletedUUIDs = [];
        foreach ($command->employees as $employee) {
            $uuid = EmployeeUUID::fromString($employee->getUUID()->toString());
            $employeeAggregate = $this->employeeAggregateReaderRepository->getEmployeeAggregateByUUID($uuid);

            $employeeAggregate->delete();

            $events = $employeeAggregate->pullEvents();
            foreach ($events as $event) {
                $this->eventStoreCreator->create(
                    new EventStore(
                        $event->uuid->toString(),
                        $event::class,
                        EmployeeAggregate::class,
                        $this->serializer->serialize($event, 'json'),
                        $this->security->getUser()->getEmployee()?->getUUID(),
                    )
                );

                $this->eventDispatcher->dispatch($event);
            }

            $deletedUUIDs[] = $uuid->toString();
        }

        $multiEvent = new EmployeeMultipleDeletedEvent($deletedUUIDs);
        $this->eventStoreCreator->create(
            new EventStore(
                Uuid::uuid4()->toString(),
                $multiEvent::class,
                EmployeeAggregate::class,
                $this->serializer->serialize($multiEvent, 'json'),
                $this->security->getUser()->getEmployee()?->getUUID(),
            )
        );
    }
}
