<?php

namespace App\Module\Company\Application\CommandHandler\Role;

use App\Common\Domain\Abstract\CommandHandlerAbstract;
use App\Module\Company\Application\Command\Role\DeleteRoleCommand;
use App\Module\Company\Application\Event\Role\RoleDeletedEvent;
use App\Module\Company\Domain\Interface\Role\RoleDeleterInterface;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'command.bus')]
final class DeleteRoleCommandHandler extends CommandHandlerAbstract
{
    public function __construct(
        private readonly RoleReaderInterface $roleReaderRepository,
        private readonly RoleDeleterInterface $roleDeleter,
        private readonly EventDispatcherInterface $eventDispatcher,
        #[AutowireIterator(tag: 'app.role.delete.validator')] protected iterable $validators,
    ) {
    }

    public function __invoke(DeleteRoleCommand $command): void
    {
        $this->validate($command);

        $role = $this->roleReaderRepository->getRoleByUUID($command->roleUUID);
        $this->roleDeleter->delete($role);
        $this->eventDispatcher->dispatch(new RoleDeletedEvent([
            DeleteRoleCommand::ROLE_UUID => $command->roleUUID,
        ]));
    }
}
