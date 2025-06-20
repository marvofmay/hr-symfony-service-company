<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Role;

use App\Module\Company\Domain\Entity\Role;

final readonly class CreateRoleAccessPermissionCommand
{
    public function __construct(private Role $role, private array $accesses)
    {
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function getAccesses(): array
    {
        return $this->accesses;
    }
}
