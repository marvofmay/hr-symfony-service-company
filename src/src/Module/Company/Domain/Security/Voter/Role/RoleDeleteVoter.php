<?php

namespace App\Module\Company\Domain\Security\Voter\Role;

use App\Module\System\Domain\Security\Voter\AccessPermissionVoter;

final class RoleDeleteVoter extends AccessPermissionVoter
{
    public const DELETE = 'delete';

    protected function getAttributeName(): string
    {
        return self::DELETE;
    }
}
