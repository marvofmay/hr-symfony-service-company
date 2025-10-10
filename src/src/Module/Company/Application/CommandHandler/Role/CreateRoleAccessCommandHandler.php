<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Role;

use App\Module\Company\Application\Command\Role\CreateRoleAccessCommand;
use App\Module\Company\Application\Event\Role\RoleAssignedAccessesEvent;
use App\Module\Company\Domain\Service\Role\RoleAccessCreator;
use App\Module\System\Domain\Entity\Access;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
readonly class CreateRoleAccessCommandHandler
{
    public function __construct(private RoleAccessCreator $roleAccessCreator, private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function __invoke(CreateRoleAccessCommand $command): void
    {
        $this->roleAccessCreator->create($command->getRole(), $command->getAccesses());
        $this->eventDispatcher->dispatch(new RoleAssignedAccessesEvent([
            'roleUUID' => $command->getRole()->getUuid(),
            'accessesUUID' => $command->getAccesses()->map(fn (Access $access) => $access->getUuid())->toArray(),
        ]));
    }
}
