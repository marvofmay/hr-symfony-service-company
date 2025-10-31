<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Role\Import;

use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Enum\Role\RoleImportColumnEnum;

final class RoleFactory
{
    public function create(array $roleData): Role
    {
        $role = new Role();
        $this->fillData($role, $roleData);

        return $role;
    }

    public function update(array $roleData, array $existingRoles): Role
    {
        $role = $existingRoles[$roleData[RoleImportColumnEnum::ROLE_NAME->value]];
        $this->fillData($role, $roleData);

        return $role;
    }

    private function fillData(Role $role, array $roleData): void
    {
        $name = $roleData[RoleImportColumnEnum::ROLE_NAME->value] ?? null;
        $description = $roleData[RoleImportColumnEnum::ROLE_DESCRIPTION->value] ?? null;

        $role->setName($name);
        $role->setDescription($description);
    }
}
