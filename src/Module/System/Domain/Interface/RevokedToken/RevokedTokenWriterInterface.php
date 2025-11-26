<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Interface\RevokedToken;

use App\Module\System\Domain\Entity\RevokedToken;

interface RevokedTokenWriterInterface
{
    public function save(RevokedToken $revokedToken): void;
}
