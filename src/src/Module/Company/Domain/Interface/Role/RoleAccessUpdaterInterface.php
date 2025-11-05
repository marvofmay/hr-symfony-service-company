<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Role;

use App\Module\Company\Domain\Entity\Role;

interface RoleAccessUpdaterInterface
{
    public function updateAccesses(Role $role, array $accessesUUIDs): void;
}