<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Role;

use App\Module\Company\Application\Command\Role\CreateRoleAccessPermissionCommand;
use App\Module\Company\Application\Event\Role\RoleAssignedPermissionsEvent;
use App\Module\Company\Domain\Service\Role\RoleAccessPermissionCreator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'command.bus')]
readonly class CreateRoleAccessPermissionCommandHandler
{
    public function __construct(private RoleAccessPermissionCreator $roleAccessPermissionCreator, private EventDispatcherInterface $eventDispatcher,)
    {
    }

    public function __invoke(CreateRoleAccessPermissionCommand $command): void
    {
        $this->roleAccessPermissionCreator->create($command->getRole(), $command->getAccesses());
        $this->eventDispatcher->dispatch(new RoleAssignedPermissionsEvent([
            'roleUUID' => $command->getRole()->getUUID(),
            'accesses' => $command->getAccesses(),
        ]));
    }
}
