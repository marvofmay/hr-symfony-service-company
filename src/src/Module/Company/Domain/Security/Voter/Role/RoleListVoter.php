<?php

namespace App\Module\Company\Domain\Security\Voter\Role;

use App\Module\System\Domain\Security\Voter\AccessPermissionVoter;

final class RoleListVoter extends AccessPermissionVoter
{
    public const LIST = 'list';

    protected function getAttributeName(): string
    {
        return self::LIST;
    }
}
