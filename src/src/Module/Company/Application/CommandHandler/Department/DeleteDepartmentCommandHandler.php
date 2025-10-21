<?php

namespace App\Module\Company\Application\CommandHandler\Department;

use App\Common\Domain\Abstract\CommandHandlerAbstract;
use App\Common\Domain\Entity\EventStore;
use App\Common\Domain\Service\EventStore\EventStoreCreator;
use App\Module\Company\Application\Command\Department\DeleteDepartmentCommand;
use App\Module\Company\Domain\Aggregate\Department\DepartmentAggregate;
use App\Module\Company\Domain\Aggregate\Department\ValueObject\DepartmentUUID;
use App\Module\Company\Domain\Interface\Department\DepartmentAggregateReaderInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'command.bus')]
final class DeleteDepartmentCommandHandler extends CommandHandlerAbstract
{
    public function __construct(
        private readonly EventDispatcherInterface                                      $eventDispatcher,
        private readonly DepartmentAggregateReaderInterface                            $departmentAggregateReaderRepository,
        private readonly EventStoreCreator                                             $eventStoreCreator,
        private readonly Security                                                      $security,
        private readonly SerializerInterface                                           $serializer,
        #[AutowireIterator(tag: 'app.department.delete.validator')] protected iterable $validators,
    ) {
    }

    public function __invoke(DeleteDepartmentCommand $command): void
    {
        $this->validate($command);

        $departmentAggregate = $this->departmentAggregateReaderRepository->getDepartmentAggregateByUUID(
            DepartmentUUID::fromString($command->departmentUUID)
        );

        $departmentAggregate->delete();

        $events = $departmentAggregate->pullEvents();
        foreach ($events as $event) {
            $this->eventStoreCreator->create(
                new EventStore(
                    $event->uuid->toString(),
                    $event::class,
                    DepartmentAggregate::class,
                    $this->serializer->serialize($event, 'json'),
                    $this->security->getUser()->getEmployee()?->getUUID(),
                )
            );

            $this->eventDispatcher->dispatch($event);
        }
    }
}
