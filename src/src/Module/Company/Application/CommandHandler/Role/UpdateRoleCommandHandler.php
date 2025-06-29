<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Role;

use App\Module\Company\Application\Command\Role\UpdateRoleCommand;
use App\Module\Company\Application\Event\Role\RoleUpdatedEvent;
use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleUpdaterInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class UpdateRoleCommandHandler
{
    public function __construct(private RoleUpdaterInterface $roleUpdater, private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function __invoke(UpdateRoleCommand $command): void
    {
        $this->roleUpdater->update($command->getRole(), $command->getName(), $command->getDescription());

        $this->eventDispatcher->dispatch(new RoleUpdatedEvent([
            Role::COLUMN_UUID        => $command->getRole()->getUUID(),
            Role::COLUMN_NAME        => $command->getName(),
            Role::COLUMN_DESCRIPTION => $command->getDescription(),
        ]));
    }
}
