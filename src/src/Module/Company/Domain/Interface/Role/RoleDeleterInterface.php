<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Role;

use App\Module\Company\Domain\Entity\Role;

interface RoleDeleterInterface
{
    public function delete(Role $role): void;
}
