<?php

namespace App\Module\System\Domain\Security\Voter;

final class UpdateVoter extends AccessPermissionVoter
{
    public const UPDATE = 'update';

    protected function getAttributeName(): string
    {
        return self::UPDATE;
    }
}
