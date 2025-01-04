<?php

declare(strict_types=1);

namespace App\Domain\Interface\Role;

use App\Domain\Entity\Role;

interface RoleWriterInterface
{
    public function saveRoleInDB (Role $role): Role;
    public function updateRoleInDB (Role $role): Role;
}