<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Role;

use App\Common\Domain\Interface\CommandInterface;
use App\Module\Company\Application\Command\Role\AssignPermissionsCommand;

class AssignPermissionsPayloadParser
{
    public function parse(CommandInterface $command): array
    {
        $result = [];
        foreach ($command->{AssignPermissionsCommand::ACCESSES} ?? [] as $access) {
            $accessUUID = $access[AssignPermissionsCommand::ACCESS_UUID] ?? null;
            $permissions = $access[AssignPermissionsCommand::PERMISSIONS_UUIDS] ?? [];

            if (!$accessUUID || !is_array($permissions)) {
                continue;
            }

            $uniquePermissions = array_unique($permissions);

            if (isset($result[$accessUUID])) {
                $result[$accessUUID] = array_unique(array_merge($result[$accessUUID], $uniquePermissions));
            } else {
                $result[$accessUUID] = $uniquePermissions;
            }
        }

        return $result;
    }
}