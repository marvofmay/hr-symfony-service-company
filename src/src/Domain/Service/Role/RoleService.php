<?php

declare(strict_types = 1);

namespace App\Domain\Service\Role;

use App\Domain\Entity\Role;
use App\Domain\Interface\Role\RoleWriterInterface;

readonly class RoleService
{
    public function __construct(private readonly RoleWriterInterface $roleWriterRepository)
    {
    }

    public function __toString()
    {
        return 'RoleService';
    }

    public function saveRoleInDB(Role $role): Role
    {
        return $this->roleWriterRepository->saveRoleInDB($role);
    }

    public function updateRoleInDB(Role $role): Role
    {
        return $this->roleWriterRepository->updateRoleInDB($role);
    }
}