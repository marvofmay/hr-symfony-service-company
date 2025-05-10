<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Service\Permission;

use App\Module\System\Domain\Interface\Permission\PermissionReaderInterface;

readonly class PermissionChecker
{
    public function __construct(private PermissionReaderInterface $permissionReaderRepository,)
    {
    }

    public function checkIsExists(string $permissionUUID): bool
    {
        return $this->permissionReaderRepository->isPermissionWithUUIDExists($permissionUUID);
    }

    public function checkIsActive(string $permissionUUID): bool
    {
        return $this->permissionReaderRepository->isPermissionActive($permissionUUID);
    }
}