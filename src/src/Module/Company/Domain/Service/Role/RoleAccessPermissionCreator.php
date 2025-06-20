<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Role;

use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;
use App\Module\System\Domain\Interface\Access\AccessReaderInterface;
use App\Module\System\Domain\Interface\Permission\PermissionReaderInterface;

readonly class RoleAccessPermissionCreator
{
    public function __construct(
        private RoleWriterInterface $roleWriterRepository,
        private AccessReaderInterface $accessReaderRepository,
        private PermissionReaderInterface $permissionReaderRepository,
    ) {
    }

    public function create(Role $role, array $accesses): void
    {
        foreach ($accesses as $item) {
            $access = $this->accessReaderRepository->getAccessByUUID($item['uuid']);
            foreach ($item['permissions'] as $permissionUUID) {
                $permission = $this->permissionReaderRepository->getPermissionByUUID($permissionUUID);
                if ($role && $access && $permission) {
                    $role->addAccessPermission($access, $permission);
                }
            }
        }

        $this->roleWriterRepository->saveRoleInDB($role);
    }
}
