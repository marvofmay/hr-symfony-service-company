<?php

namespace App\module\company\Application\Command\Role;

use App\module\company\Domain\Entity\Role;

readonly class DeleteRoleCommand
{
    public function __construct(private Role $role) {}

    public function getRole(): Role
    {
        return $this->role;
    }
}