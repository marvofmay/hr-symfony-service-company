<?php

namespace App\Module\Company\Application\CommandHandler\Employee;

use App\Common\Domain\Entity\EventStore;
use App\Common\Domain\Service\EventStore\EventStoreCreator;
use App\Module\Company\Application\Command\Employee\DeleteEmployeeCommand;
use App\Module\Company\Domain\Aggregate\Employee\EmployeeAggregate;
use App\Module\Company\Domain\Aggregate\Employee\ValueObject\EmployeeUUID;
use App\Module\Company\Domain\Interface\Employee\EmployeeAggregateReaderInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class DeleteEmployeeCommandHandler
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private EmployeeAggregateReaderInterface $employeeAggregateReaderRepository,
        private EventStoreCreator $eventStoreCreator,
        private Security $security,
        private SerializerInterface $serializer,
    ) {
    }

    public function __invoke(DeleteEmployeeCommand $command): void
    {
        $employeeAggregate = $this->employeeAggregateReaderRepository->getEmployeeAggregateByUUID(
            EmployeeUUID::fromString($command->getEmployee()->getUUID()->toString())
        );

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
    }
}
