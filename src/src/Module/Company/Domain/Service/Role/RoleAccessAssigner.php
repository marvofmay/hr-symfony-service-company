<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Role;

use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleAccessAssignerInterface;
use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;

final readonly class RoleAccessAssigner implements RoleAccessAssignerInterface
{
    public function __construct(
        private RoleWriterInterface $roleWriterRepository,
        private RoleAccessUpdater  $roleAccessUpdater,
    )
    {
    }

    public function assign(Role $role, array $accessesUUIDs): void
    {
        $this->roleAccessUpdater->updateAccesses(role: $role, accessesUUIDs: $accessesUUIDs);

        $this->roleWriterRepository->saveRoleInDB($role);
    }
}
