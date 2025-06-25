<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Role;

use App\Module\Company\Application\Command\Role\DeleteMultipleRolesCommand;
use App\Module\Company\Application\Event\Role\RoleMultipleDeletedEvent;
use App\Module\Company\Domain\Service\Role\RoleMultipleDeleter;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'command.bus')]
readonly class DeleteMultipleRolesCommandHandler
{
    public function __construct(private RoleMultipleDeleter $roleMultipleDeleter, private EventDispatcherInterface $eventDispatcher,)
    {
    }

    public function __invoke(DeleteMultipleRolesCommand $command): void
    {
        $this->roleMultipleDeleter->multipleDelete($command->roles);
        $this->eventDispatcher->dispatch(new RoleMultipleDeletedEvent(
            $command->roles->map(fn($role) => $role->getUUID())->toArray()
        ));
    }
}
