<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Role;

use App\Common\Domain\Abstract\CommandHandlerAbstract;
use App\Module\Company\Application\Command\Role\UpdateRoleCommand;
use App\Module\Company\Application\Event\Role\RoleUpdatedEvent;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use App\Module\Company\Domain\Interface\Role\RoleUpdaterInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'command.bus')]
final class UpdateRoleCommandHandler extends CommandHandlerAbstract
{
    public function __construct(
        private readonly RoleReaderInterface $roleReaderRepository,
        private readonly RoleUpdaterInterface $roleUpdater,
        private readonly EventDispatcherInterface $eventDispatcher,
        #[AutowireIterator(tag: 'app.role.update.validator')] protected iterable $validators,
    ) {
    }

    public function __invoke(UpdateRoleCommand $command): void
    {
        $this->validate($command);

        $role = $this->roleReaderRepository->getRoleByUUID($command->roleUUID);
        $this->roleUpdater->update($role, $command->name, $command->description);

        $this->eventDispatcher->dispatch(new RoleUpdatedEvent([
            UpdateRoleCommand::ROLE_UUID => $command->roleUUID,
            UpdateRoleCommand::ROLE_NAME => $command->name,
            UpdateRoleCommand::ROLE_DESCRIPTION => $command->description,
        ]));
    }
}
