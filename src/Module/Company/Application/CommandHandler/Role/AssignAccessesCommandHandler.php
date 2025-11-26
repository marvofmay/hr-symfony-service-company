<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Role;

use App\Common\Domain\Abstract\CommandHandlerAbstract;
use App\Module\Company\Application\Command\Role\AssignAccessesCommand;
use App\Module\Company\Application\Event\Role\RoleAssignedAccessesEvent;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use App\Module\Company\Domain\Service\Role\RoleAccessAssigner;
use App\Module\Company\Domain\Service\Role\RoleAccessDeleter;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final class AssignAccessesCommandHandler extends CommandHandlerAbstract
{
    public function __construct(
        private readonly RoleReaderInterface $roleReaderRepository,
        private readonly RoleAccessAssigner $roleAccessAssigner,
        private readonly RoleAccessDeleter $roleAccessDeleter,
        private readonly EventDispatcherInterface $eventDispatcher,
        #[AutowireIterator(tag: 'app.role.assignAccesses.validator')] protected iterable $validators,
    ) {
    }

    public function __invoke(AssignAccessesCommand $command): void
    {
        $this->validate($command);

        $role = $this->roleReaderRepository->getRoleByUUID($command->roleUUID);
        if (!empty($command->accessesUUIDs)) {
            $this->roleAccessAssigner->assign(role: $role, accessesUUIDs: $command->accessesUUIDs);
        } else {
            $this->roleAccessDeleter->delete($role);
        }

        $this->eventDispatcher->dispatch(new RoleAssignedAccessesEvent([
            AssignAccessesCommand::ROLE_UUID      => $command->roleUUID,
            AssignAccessesCommand::ACCESSES_UUIDS => $command->accessesUUIDs,
        ]));
    }
}
