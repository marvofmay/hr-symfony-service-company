<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Role;

use App\Module\Company\Domain\Entity\Role;

interface RoleUpdaterInterface
{
    public function update(Role $role, string $name, ?string $description = null): void;
}
