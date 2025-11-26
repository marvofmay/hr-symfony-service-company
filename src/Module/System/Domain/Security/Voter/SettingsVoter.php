<?php

namespace App\Module\System\Domain\Security\Voter;

final class SettingsVoter extends AccessPermissionVoter
{
    public const string SETTINGS = 'settings';

    protected function getAttributeName(): string
    {
        return self::SETTINGS;
    }
}
