<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Role;

use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;
use App\Module\System\Domain\Interface\Access\AccessReaderInterface;
use App\Module\System\Domain\Interface\Permission\PermissionReaderInterface;

readonly class RoleAccessPermissionCreator
{
    public function __construct(
        private RoleWriterInterface $roleWriterRepository,
        private RoleReaderInterface $roleReaderRepository,
        private AccessReaderInterface $accessReaderRepository,
        private PermissionReaderInterface $permissionReaderRepository
    ) {}

    public function create(string $roleUUID, array $accesses): void
    {
        $role = $this->roleReaderRepository->getRoleByUUID($roleUUID);
        foreach ($accesses as $item) {
            $access = $this->accessReaderRepository->getAccessByUUID($item['uuid']);
            foreach ($item['permissions'] as $permissionUUID) {
                $permission = $this->permissionReaderRepository->getPermissionByUUID($permissionUUID);
                if ($role && $access && $permission) {
                    $role->addAccessPermission($access, $permission);
                }
            }
        }

        $this->roleWriterRepository->saveOrUpdateRoleInDB($role);
    }
}