<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Role;

use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleAccessPermissionUpdaterInterface;
use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;
use App\Module\System\Domain\Entity\Access;

readonly class RoleAccessPermissionAssigner
{
    public function __construct(
        private RoleWriterInterface $roleWriterRepository,
        private RoleAccessPermissionUpdaterInterface $roleAccessPermissionUpdater,
    ) {
    }

    public function assign(Role $role, Access $access, array $permissionsUUIDs): void
    {
        $this->roleAccessPermissionUpdater->updateAccessesPermission($role, $access, $permissionsUUIDs);
        $this->roleWriterRepository->saveRoleInDB($role);
    }
}
