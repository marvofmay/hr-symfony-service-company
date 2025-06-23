<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Role;

use App\Module\Company\Application\Command\Role\UpdateRoleCommand;
use App\Module\Company\Domain\Service\Role\RoleUpdater;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class UpdateRoleCommandHandler
{
    public function __construct(private RoleUpdater $roleUpdater)
    {
    }

    public function __invoke(UpdateRoleCommand $command): void
    {
        $this->roleUpdater->update($command->getRole(), $command->getName(), $command->getDescription());
    }
}
