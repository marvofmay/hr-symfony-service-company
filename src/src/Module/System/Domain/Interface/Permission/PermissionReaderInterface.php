<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Interface\Permission;

use App\Module\System\Domain\Entity\Permission;

interface PermissionReaderInterface
{
    public function getPermissionByUUID(string $uuid): ?Permission;
    public function getPermissionByName(string $name): ?Permission;
    public function isPermissionWithUUIDExists(string $uuid): bool;
    public function isPermissionWithNameExists(string $name): bool;
    public function isPermissionActive(string $uuid): bool;
}