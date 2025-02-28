<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Role;

use App\Module\Company\Application\Command\Role\DeleteMultipleRolesCommand;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use App\Module\Company\Domain\Service\Role\RoleService;

readonly class DeleteMultipleRolesCommandHandler
{
    public function __construct(private RoleService $roleService, private RoleReaderInterface $roleReaderRepository)
    {
    }

    public function __invoke(DeleteMultipleRolesCommand $command): void
    {
        $this->roleService->deleteMultipleRolesInDB($this->roleReaderRepository->getRolesByUUID($command->selectedUUID));
    }
}
