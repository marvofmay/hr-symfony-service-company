<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Role;

use App\Module\Company\Application\Command\Role\AssignAccessesCommand;
use App\Module\Company\Domain\Entity\Role;
use App\Module\System\Domain\Interface\Access\AccessReaderInterface;

final readonly class RoleAccessUpdater
{
    public function __construct(
        private AccessReaderInterface $accessReaderRepository,
        private AccessSynchronizer $accessSynchronizer,
    ) {
    }

    public function updateAccesses(Role $role, AssignAccessesCommand $command): void
    {
        $accesses = $this->accessReaderRepository->getAccesses()->toArray();

        $existingAccesses = [];
        $payloadAccessesUUIDs = [];

        foreach ($accesses as $access) {
            $existingAccesses[$access->getUUID()->toString()] = $access;
        }

        foreach ($existingAccesses as $existingAccess) {
            if (in_array($existingAccess->getUUID()->toString(), $command->accessesUUIDs, true)) {
                $payloadAccessesUUIDs[] = $existingAccess->getUUID()->toString();
            }
        }

        $this->accessSynchronizer->syncAccesses(
            $role,
            $payloadAccessesUUIDs,
            $existingAccesses
        );
    }
}
