<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Role;

use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleAccessPermissionUpdaterInterface;
use App\Module\System\Domain\Entity\Access;
use App\Module\System\Domain\Interface\Permission\PermissionReaderInterface;

final readonly class RoleAccessPermissionUpdater implements RoleAccessPermissionUpdaterInterface
{
    public function __construct(
        private PermissionReaderInterface $permissionReaderRepository,
        private AccessPermissionSynchronizer $accessPermissionSynchronizer,
    ) {
    }

    public function updateAccessesPermission(Role $role, Access $access, array $permissionsUUIDs): void
    {
        $existingPermissions = [];
        $permissions = $this->permissionReaderRepository->getPermissions();
        foreach ($permissions as $permission) {
            $existingPermissions[$permission->getUUID()->toString()] = $permission;
        }

        $this->accessPermissionSynchronizer->syncAccessPermissions(
            role: $role,
            access: $access,
            payloadPermissionsUUIDs: $permissionsUUIDs,
            existingPermissions: $existingPermissions
        );
    }
}
