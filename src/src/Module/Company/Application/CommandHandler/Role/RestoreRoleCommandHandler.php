<?php

namespace App\Module\Company\Application\CommandHandler\Role;

use App\Common\Domain\Abstract\CommandHandlerAbstract;
use App\Module\Company\Application\Command\Role\RestoreRoleCommand;
use App\Module\Company\Application\Event\Role\RoleRestoredEvent;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use App\Module\Company\Domain\Service\Role\RoleRestorer;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'command.bus')]
final class RestoreRoleCommandHandler extends CommandHandlerAbstract
{
    public function __construct(
        private readonly RoleReaderInterface $roleReaderRepository,
        private readonly RoleRestorer $roleRestorer,
        private readonly EventDispatcherInterface $eventDispatcher,
        #[AutowireIterator(tag: 'app.role.restore.validator')] protected iterable $validators,
    ) {
    }

    public function __invoke(RestoreRoleCommand $command): void
    {
        $this->validate($command);

        $role = $this->roleReaderRepository->getDeletedRoleByUUID($command->roleUUID);
        $this->roleRestorer->restore($role);
        $this->eventDispatcher->dispatch(new RoleRestoredEvent([
            RestoreRoleCommand::ROLE_UUID => $command->roleUUID,
        ]));
    }
}
