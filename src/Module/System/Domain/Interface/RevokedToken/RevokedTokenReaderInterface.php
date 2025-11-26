<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Interface\RevokedToken;

use App\Module\System\Domain\Entity\RevokedToken;
use App\Module\System\Domain\ValueObject\TokenUUID;

interface RevokedTokenReaderInterface
{
    public function getByTokenUUID(TokenUUID $tokenUUID): ?RevokedToken;
    public function isRevoked(TokenUUID $tokenUUID): bool;
}
