<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Service\RoleAccess;

use App\Module\Company\Domain\Entity\Role;
use App\Module\System\Domain\Entity\Access;
use App\Module\System\Domain\Interface\RoleAccess\RoleAccessInterface;

readonly class RoleAccessChecker
{
    public function __construct(private RoleAccessInterface $roleAccessRepository)
    {
    }

    public function check(Access $access, Role $role): bool
    {
        return $this->roleAccessRepository->isRoleHasAccess($access, $role);
    }
}