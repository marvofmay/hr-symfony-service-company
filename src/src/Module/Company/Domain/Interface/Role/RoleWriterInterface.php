<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Role;

use App\Module\Company\Domain\Entity\Role;

interface RoleWriterInterface
{
    public function saveRoleInDB (Role $role): void;
    public function updateRoleInDB (Role $role): void;
    public function saveRolesInDB (array $roles): void;
}