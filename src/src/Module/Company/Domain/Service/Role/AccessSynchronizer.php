<?php

namespace App\Module\Company\Domain\Service\Role;

use App\Common\Domain\Enum\DeleteTypeEnum;
use App\Module\Company\Domain\Entity\Role;
use App\Module\System\Domain\Interface\RoleAccess\RoleAccessWriterInterface;

final class AccessSynchronizer
{
    public function __construct(
        private readonly RoleAccessWriterInterface $roleAccessWriterRepository,
    ) {}

    public function syncAccesses(Role $role, array $payloadAccessUUIDs, array $existingAccesses): void
    {
        $remainingUUIDs = $payloadAccessUUIDs;
        foreach ($role->getAccesses() as $currentAccess) {
            $uuid = $currentAccess->getUUID()->toString();
            if (in_array($uuid, $remainingUUIDs, true)) {
                $remainingUUIDs = array_values(array_filter(
                    $remainingUUIDs,
                    fn (string $code) => $code !== $uuid
                ));
                continue;
            }

            $role->removeAccess($currentAccess);

            $this->roleAccessWriterRepository->deleteRoleAccessByRoleAndAccessInDB(
                $role,
                $currentAccess,
                DeleteTypeEnum::HARD_DELETE
            );
        }

        $accessesToAdd = array_intersect_key(
            $existingAccesses,
            array_flip($remainingUUIDs)
        );

        foreach ($accessesToAdd as $access) {
            $role->addAccess($access);
        }
    }
}