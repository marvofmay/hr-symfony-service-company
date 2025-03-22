<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Interface\Access;

use App\Module\System\Domain\Entity\Access;
interface AccessReaderInterface
{
    public function getAccessByUUID(string $uuid): ?Access;
    public function isAccessWithUUIDExists(string $uuid): bool;
}