<?php

namespace App\Module\System\Domain\Security\Voter;

final class CreateVoter extends AccessPermissionVoter
{
    public const CREATE = 'create';

    protected function getAttributeName(): string
    {
        return self::CREATE;
    }
}
