<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Role;

use App\Module\Company\Domain\Entity\Role;

interface AccessSynchronizerInterface
{
    public function syncAccesses(Role $role, array $accessUUIDs, array $existingAccesses): void;
}
