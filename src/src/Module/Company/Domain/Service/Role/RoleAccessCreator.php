<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Role;

use App\Module\Company\Application\Command\Role\AssignAccessesCommand;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;
use App\Module\System\Domain\Interface\Access\AccessReaderInterface;

final readonly class RoleAccessCreator
{
    public function __construct(
        private RoleReaderInterface $roleReaderRepository,
        private RoleWriterInterface $roleWriterRepository,
        private AccessReaderInterface $accessReaderRepository,
        private RoleAccessUpdater  $roleAccessUpdater,
    )
    {
    }

    public function create(AssignAccessesCommand $command): void
    {
        $role = $this->roleReaderRepository->getRoleByUUID($command->roleUUID);
        $accesses = $this->accessReaderRepository->getAccessesByUUID($command->accessesUUIDs);
        foreach ($accesses as $access) {
            $role->addAccess($access);
        }

        $this->roleAccessUpdater->updateAccesses($role, $command);

        $this->roleWriterRepository->saveRoleInDB($role);
    }
}
