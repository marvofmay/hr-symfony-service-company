<?php

namespace App\Module\Company\Domain\Security\Voter\Role;

use App\Module\System\Domain\Security\Voter\AccessPermissionVoter;

final class RoleImportVoter extends AccessPermissionVoter
{
    public const IMPORT = 'import';

    protected function getAttributeName(): string
    {
        return self::IMPORT;
    }
}
