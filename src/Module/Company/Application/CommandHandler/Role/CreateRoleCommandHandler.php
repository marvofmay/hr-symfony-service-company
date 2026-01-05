<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Role;

use App\Common\Domain\Abstract\CommandHandlerAbstract;
use App\Module\Company\Application\Command\Role\CreateRoleCommand;
use App\Module\Company\Application\Event\Role\RoleCreatedEvent;
use App\Module\Company\Domain\Interface\Role\RoleCreatorInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'command.bus')]
final class CreateRoleCommandHandler extends CommandHandlerAbstract
{
    public function __construct(
        private readonly RoleCreatorInterface $roleCreator,
        private readonly EventDispatcherInterface $eventDispatcher,
        #[AutowireIterator(tag: 'app.role.create.validator')] protected iterable $validators,
    ) {
    }

    public function __invoke(CreateRoleCommand $command): void
    {
        $this->validate($command);

        $this->roleCreator->create(name: $command->name, description: $command->description);

        $this->eventDispatcher->dispatch(new RoleCreatedEvent([
            CreateRoleCommand::ROLE_NAME => $command->name,
            CreateRoleCommand::ROLE_DESCRIPTION => $command->description,
        ]));
    }
}
