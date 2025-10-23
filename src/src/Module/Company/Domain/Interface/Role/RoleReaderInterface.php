<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Role;

use App\Module\Company\Domain\Entity\Role;
use Doctrine\Common\Collections\Collection;

interface RoleReaderInterface
{
    public function getRoleByUUID(string $uuid): ?Role;

    public function getRoleByName(string $name, ?string $uuid): ?Role;

    public function getRolesByUUID(array $selectedUUID): Collection;

    public function isRoleNameAlreadyExists(string $name, ?string $uuid = null): bool;

    public function isRoleWithUUIDExists(string $uuid): bool;
}
