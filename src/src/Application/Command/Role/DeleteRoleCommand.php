<?php

namespace App\Application\Command\Role;

use App\Domain\Entity\Role;

readonly class DeleteRoleCommand
{
    public function __construct(private Role $role) {}

    public function getRole(): Role
    {
        return $this->role;
    }
}