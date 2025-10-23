<?php

namespace App\Module\Company\Application\CommandHandler\Department;

use App\Common\Domain\Abstract\CommandHandlerAbstract;
use App\Common\Domain\Service\EventStore\EventStoreCreator;
use App\Common\Domain\Trait\HandleEventStoreTrait;
use App\Module\Company\Application\Command\Department\RestoreDepartmentCommand;
use App\Module\Company\Domain\Aggregate\Department\DepartmentAggregate;
use App\Module\Company\Domain\Aggregate\Department\ValueObject\DepartmentUUID;
use App\Module\Company\Domain\Interface\Department\DepartmentAggregateReaderInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'command.bus')]
final class RestoreDepartmentCommandHandler extends CommandHandlerAbstract
{
    use HandleEventStoreTrait;

    public function __construct(
        private readonly DepartmentAggregateReaderInterface $departmentAggregateReaderRepository,
        private readonly EventStoreCreator $eventStoreCreator,
        private readonly Security $security,
        private readonly SerializerInterface $serializer,
        private readonly EventDispatcherInterface $eventDispatcher,
        #[AutowireIterator(tag: 'app.department.restore.validator')] protected iterable $validators,
    ) {
    }

    public function __invoke(RestoreDepartmentCommand $command): void
    {
        $this->validate($command);

        $departmentAggregate = $this->departmentAggregateReaderRepository->getDepartmentAggregateByUUID(
            DepartmentUUID::fromString($command->departmentUUID)
        );

        $departmentAggregate->restore();

        $events = $departmentAggregate->pullEvents();
        foreach ($events as $event) {
            $this->handleEvent($event, DepartmentAggregate::class);
        }
    }
}
