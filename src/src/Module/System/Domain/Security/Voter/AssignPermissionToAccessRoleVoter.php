<?php

namespace App\Module\System\Domain\Security\Voter;

final class AssignPermissionToAccessRoleVoter extends AccessPermissionVoter
{
    public const ASSIGN_PERMISSION_TO_ACCESS_ROLE = 'assign_permission_to_access_role';

    protected function getAttributeName(): string
    {
        return self::ASSIGN_PERMISSION_TO_ACCESS_ROLE;
    }
}
