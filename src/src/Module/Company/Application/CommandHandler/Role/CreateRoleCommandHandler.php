<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Role;

use App\Module\Company\Application\Command\Role\CreateRoleCommand;
use App\Module\Company\Application\Event\Role\RoleCreatedEvent;
use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Service\Role\RoleCreator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'command.bus')]
readonly class CreateRoleCommandHandler
{
    public function __construct(private RoleCreator $roleCreator, private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function __invoke(CreateRoleCommand $command): void
    {
        $this->roleCreator->create($command->getName(), $command->getDescription());
        $this->eventDispatcher->dispatch(new RoleCreatedEvent([
            Role::COLUMN_NAME        => $command->getName(),
            Role::COLUMN_DESCRIPTION => $command->getDescription(),
        ]));
    }
}
