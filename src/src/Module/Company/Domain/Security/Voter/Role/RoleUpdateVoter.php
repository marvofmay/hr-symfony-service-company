<?php

namespace App\Module\Company\Domain\Security\Voter\Role;

use App\Module\System\Domain\Security\Voter\AccessPermissionVoter;

final class RoleUpdateVoter extends AccessPermissionVoter
{
    public const UPDATE = 'update';

    protected function getAttributeName(): string
    {
        return self::UPDATE;
    }
}
