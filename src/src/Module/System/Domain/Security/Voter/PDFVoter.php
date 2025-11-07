<?php

namespace App\Module\System\Domain\Security\Voter;

final class PDFVoter extends AccessPermissionVoter
{
    public const string PDF = 'pdf';

    protected function getAttributeName(): string
    {
        return self::PDF;
    }
}
