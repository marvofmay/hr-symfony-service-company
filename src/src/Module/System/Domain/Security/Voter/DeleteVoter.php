<?php

namespace App\Module\System\Domain\Security\Voter;

final class DeleteVoter extends AccessPermissionVoter
{
    public const DELETE = 'delete';

    protected function getAttributeName(): string
    {
        return self::DELETE;
    }
}
