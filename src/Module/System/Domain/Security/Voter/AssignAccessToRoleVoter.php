<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Security\Voter;

final class AssignAccessToRoleVoter extends AccessPermissionVoter
{
    public const string ASSIGN_ACCESS_TO_ROLE = 'assign_access_to_role';

    protected function getAttributeName(): string
    {
        return self::ASSIGN_ACCESS_TO_ROLE;
    }
}
