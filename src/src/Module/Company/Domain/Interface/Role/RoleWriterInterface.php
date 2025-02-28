<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Role;

use App\Module\Company\Domain\Entity\Role;
use Doctrine\Common\Collections\Collection;

interface RoleWriterInterface
{
    public function saveRoleInDB(Role $role): void;

    public function updateRoleInDB(Role $role): void;

    public function saveRolesInDB(array $roles): void;

    public function deleteMultipleRolesInDB(Collection $roles): void;
}
