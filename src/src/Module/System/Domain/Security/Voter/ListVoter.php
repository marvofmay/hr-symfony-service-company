<?php

namespace App\Module\System\Domain\Security\Voter;

final class ListVoter extends AccessPermissionVoter
{
    public const string LIST = 'list';

    protected function getAttributeName(): string
    {
        return self::LIST;
    }
}
