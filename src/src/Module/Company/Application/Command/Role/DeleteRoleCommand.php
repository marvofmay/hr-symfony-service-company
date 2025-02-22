<?php

namespace App\Module\Company\Application\Command\Role;

use App\Module\Company\Domain\Entity\Role;

readonly class DeleteRoleCommand
{
    public function __construct(private Role $role)
    {
    }

    public function getRole(): Role
    {
        return $this->role;
    }
}
