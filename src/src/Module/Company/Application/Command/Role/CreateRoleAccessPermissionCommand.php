<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Role;

readonly class CreateRoleAccessPermissionCommand
{
    public function __construct(private string $roleUUID, private array $accesses)
    {
    }
    public function getRoleUUID(): string
    {
        return $this->roleUUID;
    }

    public function getAccesses(): array
    {
        return $this->accesses;
    }
}
