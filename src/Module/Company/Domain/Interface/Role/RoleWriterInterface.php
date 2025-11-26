<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Role;

use App\Module\Company\Domain\Entity\Role;
use Doctrine\Common\Collections\Collection;

interface RoleWriterInterface
{
    public function saveRole(Role $role): void;

    public function saveRoles(Collection $roles): void;

    public function deleteMultipleRoles(Collection $roles): void;

    public function deleteRole(Role $role): void;
}
