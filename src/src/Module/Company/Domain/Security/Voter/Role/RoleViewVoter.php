<?php

namespace App\Module\Company\Domain\Security\Voter\Role;

use App\Module\System\Domain\Security\Voter\AccessPermissionVoter;

final class RoleViewVoter extends AccessPermissionVoter
{
    public const VIEW = 'view';

    protected function getAttributeName(): string
    {
        return self::VIEW;
    }
}
