<?php

namespace App\Module\System\Domain\Security\Voter;

final class ImportVoter extends AccessPermissionVoter
{
    public const IMPORT = 'import';

    protected function getAttributeName(): string
    {
        return self::IMPORT;
    }
}
