<?php

namespace App\Module\Company\Domain\Security\Voter\Role;

use App\Module\System\Domain\Security\Voter\AccessPermissionVoter;

final class RoleCreateVoter extends AccessPermissionVoter
{
    public const CREATE = 'create';

    protected function getAttributeName(): string
    {
        return self::CREATE;
    }
}
