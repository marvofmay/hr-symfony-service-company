<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Role;

use App\Module\Company\Application\Command\Role\AssignAccessesCommand;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;

final readonly class RoleAccessAssigner
{
    public function __construct(
        private RoleReaderInterface $roleReaderRepository,
        private RoleWriterInterface $roleWriterRepository,
        private RoleAccessUpdater  $roleAccessUpdater,
    )
    {
    }

    public function assign(AssignAccessesCommand $command): void
    {
        $role = $this->roleReaderRepository->getRoleByUUID($command->roleUUID);
        $this->roleAccessUpdater->updateAccesses($role, $command->accessesUUIDs);

        $this->roleWriterRepository->saveRoleInDB($role);
    }
}
