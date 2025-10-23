<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Department;

use App\Common\Domain\Abstract\CommandHandlerAbstract;
use App\Common\Domain\Service\EventStore\EventStoreCreator;
use App\Common\Domain\Trait\HandleEventStoreTrait;
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
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'command.bus')]
final class CreateDepartmentCommandHandler extends CommandHandlerAbstract
{
    use HandleEventStoreTrait;

    public function __construct(
        private readonly EventStoreCreator $eventStoreCreator,
        private readonly Security $security,
        private readonly SerializerInterface $serializer,
        private readonly EventDispatcherInterface $eventDispatcher,
        #[AutowireIterator(tag: 'app.department.create.validator')] protected iterable $validators,
    ) {
    }

    public function __invoke(CreateDepartmentCommand $command): void
    {
        $this->validate($command);

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
            $this->handleEvent($event, DepartmentAggregate::class);
        }
    }
}
