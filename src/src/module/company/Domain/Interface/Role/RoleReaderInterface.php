<?php

declare(strict_types = 1);

namespace App\module\company\Domain\Interface\Role;

use App\module\company\Domain\Entity\Role;

interface RoleReaderInterface
{
    public function getRoleByUUID(string $uuid): ?Role;
    //public function getNotDeletedRoleByUUID(string $uuid): ?Role;
    //public function getRoles(): mixed;
}
