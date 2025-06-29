<?php

namespace App\Module\Company\Application\CommandHandler\Role;

use App\Module\Company\Application\Command\Role\DeleteRoleCommand;
use App\Module\Company\Application\Event\Role\RoleDeletedEvent;
use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleDeleterInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'command.bus')]
readonly class DeleteRoleCommandHandler
{
    public function __construct(private RoleDeleterInterface $roleDeleter, private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function __invoke(DeleteRoleCommand $command): void
    {
        $this->roleDeleter->delete($command->getRole());
        $this->eventDispatcher->dispatch(new RoleDeletedEvent([
            Role::COLUMN_UUID => $command->getRole()->getUUID(),
        ]));
    }
}
