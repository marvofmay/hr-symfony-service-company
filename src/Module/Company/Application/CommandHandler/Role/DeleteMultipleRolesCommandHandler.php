<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Role;

use App\Common\Domain\Abstract\CommandHandlerAbstract;
use App\Module\Company\Application\Command\Role\DeleteMultipleRolesCommand;
use App\Module\Company\Application\Event\Role\RoleMultipleDeletedEvent;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use App\Module\Company\Domain\Service\Role\RoleMultipleDeleter;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'command.bus')]
final class DeleteMultipleRolesCommandHandler extends CommandHandlerAbstract
{
    public function __construct(
        private readonly RoleReaderInterface $roleReaderRepository,
        private readonly RoleMultipleDeleter $roleMultipleDeleter,
        private readonly EventDispatcherInterface $eventDispatcher,
        #[AutowireIterator(tag: 'app.role.delete_multiple.validator')] protected iterable $validators,
    ) {
    }

    public function __invoke(DeleteMultipleRolesCommand $command): void
    {
        $this->validate($command);

        $roles = $this->roleReaderRepository->getRolesByUUIDs($command->rolesUUIDs);
        $this->roleMultipleDeleter->multipleDelete($roles);

        $this->eventDispatcher->dispatch(new RoleMultipleDeletedEvent([
            DeleteMultipleRolesCommand::ROLES_UUIDS => $command->rolesUUIDs,
        ]));
    }
}
