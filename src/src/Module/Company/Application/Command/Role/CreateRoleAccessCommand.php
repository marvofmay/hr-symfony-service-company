<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Role;

readonly class CreateRoleAccessCommand
{
    public function __construct(private string $roleUUID, private array $accessUUID)
    {
    }
    public function getRoleUUID(): string
    {
        return $this->roleUUID;
    }

    public function getAccessUUID(): array
    {
        return $this->accessUUID;
    }
}
