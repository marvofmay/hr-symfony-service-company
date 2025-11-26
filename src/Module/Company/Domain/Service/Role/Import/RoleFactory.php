<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Role\Import;

use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Enum\Role\RoleImportColumnEnum;

final class RoleFactory
{
    public function create(array $roleData): Role
    {
        return Role::create(
            name: trim($roleData[RoleImportColumnEnum::ROLE_NAME->value] ?? ''),
            description: trim($roleData[RoleImportColumnEnum::ROLE_DESCRIPTION->value] ?? null)
        );
    }

    public function update(Role $role, array $roleData): Role
    {
        $role->rename(trim($roleData[RoleImportColumnEnum::ROLE_NAME->value] ?? ''));
        if (null !== $roleData[RoleImportColumnEnum::ROLE_DESCRIPTION->value]) {
            $role->updateDescription(trim($roleData[RoleImportColumnEnum::ROLE_DESCRIPTION->value]));
        }

        return $role;
    }
}
