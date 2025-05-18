<?php

namespace App\Module\System\Domain\Security\Voter;

final class ViewVoter extends AccessPermissionVoter
{
    public const VIEW = 'view';

    protected function getAttributeName(): string
    {
        return self::VIEW;
    }
}
