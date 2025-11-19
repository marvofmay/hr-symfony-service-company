<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Employee;

use App\Common\Domain\Abstract\CommandHandlerAbstract;
use App\Common\Domain\Service\EventStore\EventStoreCreator;
use App\Common\Domain\Trait\HandleEventStoreTrait;
use App\Module\Company\Application\Command\Employee\DeleteMultipleEmployeesCommand;
use App\Module\Company\Domain\Aggregate\Employee\EmployeeAggregate;
use App\Module\Company\Domain\Aggregate\Employee\ValueObject\EmployeeUUID;
use App\Module\Company\Domain\Event\Employee\EmployeeMultipleDeletedEvent;
use App\Module\Company\Domain\Interface\Employee\EmployeeAggregateReaderInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'command.bus')]
final class DeleteMultipleEmployeesCommandHandler extends CommandHandlerAbstract
{
    use HandleEventStoreTrait;

    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly EmployeeAggregateReaderInterface $employeeAggregateReaderRepository,
        private readonly EventStoreCreator $eventStoreCreator,
        private readonly Security $security,
        private readonly SerializerInterface $serializer,
        #[Autowire(service: 'event.bus')] private readonly MessageBusInterface $eventBus,
        #[AutowireIterator(tag: 'app.employee.delete_multiple.validator')] protected iterable $validators,
    ) {
    }

    public function __invoke(DeleteMultipleEmployeesCommand $command): void
    {
        $this->validate($command);

        $deletedUUIDs = [];
        foreach ($command->employeesUUIDs as $employeeUUID) {
            $uuid = EmployeeUUID::fromString($employeeUUID);
            $employeeAggregate = $this->employeeAggregateReaderRepository->getEmployeeAggregateByUUID($uuid);

            $employeeAggregate->delete();

            $events = $employeeAggregate->pullEvents();
            foreach ($events as $event) {
                $this->handleEvent($event, EmployeeAggregate::class);
            }

            $deletedUUIDs[] = $uuid->toString();
        }

        $multiEvent = new EmployeeMultipleDeletedEvent($deletedUUIDs);
        $this->handleEvent($multiEvent, EmployeeAggregate::class);
    }
}
