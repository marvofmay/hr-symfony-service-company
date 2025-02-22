<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Role;

use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;

readonly class RoleService
{
    public function __construct(private RoleWriterInterface $roleWriterRepository)
    {
    }

    public function __toString()
    {
        return 'RoleService';
    }

    public function saveRoleInDB(Role $role): void
    {
        $this->roleWriterRepository->saveRoleInDB($role);
    }

    public function updateRoleInDB(Role $role): void
    {
        $this->roleWriterRepository->updateRoleInDB($role);
    }

    public function saveRolesInDB(array $roles): void
    {
        $this->roleWriterRepository->saveRolesInDB($roles);
    }

    public function deleteMultipleRolesInDB(array $selectedUUID): void
    {
        $this->roleWriterRepository->deleteMultipleRolesInDB($selectedUUID);
    }
}
