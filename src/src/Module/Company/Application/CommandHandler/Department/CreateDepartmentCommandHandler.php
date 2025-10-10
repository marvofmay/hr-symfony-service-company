<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Department;

use App\Common\Domain\Entity\EventStore;
use App\Common\Domain\Service\EventStore\EventStoreCreator;
use App\Module\Company\Application\Command\Department\CreateDepartmentCommand;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\CompanyUUID;
use App\Module\Company\Domain\Aggregate\Department\DepartmentAggregate;
use App\Module\Company\Domain\Aggregate\Department\ValueObject\DepartmentUUID;
use App\Module\Company\Domain\Aggregate\Department\ValueObject\Name;
use App\Module\Company\Domain\Aggregate\ValueObject\Address;
use App\Module\Company\Domain\Aggregate\ValueObject\Emails;
use App\Module\Company\Domain\Aggregate\ValueObject\Phones;
use App\Module\Company\Domain\Aggregate\ValueObject\Websites;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class CreateDepartmentCommandHandler
{
    public function __construct(
        private EventStoreCreator $eventStoreCreator,
        private Security $security,
        private SerializerInterface $serializer,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(CreateDepartmentCommand $command): void
    {
        $departmentAggregate = DepartmentAggregate::create(
            CompanyUUID::fromString($command->companyUUID),
            Name::fromString($command->name),
            $command->internalCode,
            Address::fromDTO($command->address),
            $command->active,
            $command->description,
            $command->phones ? Phones::fromArray($command->phones) : null,
            $command->emails ? Emails::fromArray($command->emails) : null,
            $command->websites ? Websites::fromArray($command->websites) : null,
            $command->parentDepartmentUUID ? DepartmentUUID::fromString($command->parentDepartmentUUID) : null,
        );

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
