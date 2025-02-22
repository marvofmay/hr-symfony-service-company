<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Position;

use App\Module\Company\Domain\Entity\Position;

interface PositionReaderInterface
{
    public function getPositionByUUID(string $uuid): ?Position;

    public function getPositionByName(string $name, ?string $uuid): ?Position;
}
