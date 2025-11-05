<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Role;

use App\Module\Company\Domain\Entity\Role;
use App\Module\System\Domain\Interface\Access\AccessReaderInterface;

final readonly class RoleAccessUpdater
{
    public function __construct(
        private AccessReaderInterface $accessReaderRepository,
        private AccessSynchronizer $accessSynchronizer,
    ) {
    }

    public function updateAccesses(Role $role, array $accessesUUIDs): void
    {
        $existingAccesses = [];
        $accesses = $this->accessReaderRepository->getAccesses()->toArray();
        foreach ($accesses as $access) {
            $existingAccesses[$access->getUUID()->toString()] = $access;
        }

        $this->accessSynchronizer->syncAccesses($role, $accessesUUIDs, $existingAccesses);
    }
}
