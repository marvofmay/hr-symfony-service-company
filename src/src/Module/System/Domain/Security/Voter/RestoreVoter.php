<?php

namespace App\Module\System\Domain\Security\Voter;

final class RestoreVoter extends AccessPermissionVoter
{
    public const RESTORE = 'restore';

    protected function getAttributeName(): string
    {
        return self::RESTORE;
    }
}
