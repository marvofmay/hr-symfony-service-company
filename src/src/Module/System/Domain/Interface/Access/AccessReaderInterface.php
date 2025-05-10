<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Interface\Access;

use App\Module\System\Domain\Entity\Access;
use App\Module\System\Domain\Entity\Module;

interface AccessReaderInterface
{
    public function getAccessByUUID(string $uuid): ?Access;
    public function getAccessByNameAndModuleUUID(string $name, Module $module): ?Access;
    public function isAccessWithUUIDExists(string $uuid): bool;
    public function isAccessActive(string $uuid): bool;
}