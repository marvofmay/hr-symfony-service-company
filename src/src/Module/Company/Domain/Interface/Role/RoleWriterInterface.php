<?php

declare(strict_types=1);

namespace App\module\company\Domain\Interface\Role;

use App\module\company\Domain\Entity\Role;

interface RoleWriterInterface
{
    public function saveRoleInDB (Role $role): Role;
    public function updateRoleInDB (Role $role): Role;
    public function saveRolesInDB (array $roles): void;
}