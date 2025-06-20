<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Role;

use App\Module\Company\Domain\Entity\Role;
use Doctrine\Common\Collections\Collection;

final readonly class CreateRoleAccessCommand
{
    public function __construct(private Role $role, private Collection $accesses)
    {
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function getAccesses(): Collection
    {
        return $this->accesses;
    }
}
