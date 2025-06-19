<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Role;

use App\Module\Company\Application\Command\Role\CreateRoleAccessPermissionCommand;
use App\Module\Company\Application\Validator\RoleAccessPermission\RoleAccessPermissionValidator;
use App\Module\Company\Domain\DTO\Role\CreateAccessPermissionDTO;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class CreateRoleAccessPermissionAction
{
    public function __construct(private MessageBusInterface $commandBus, private RoleReaderInterface $roleReaderRepository, private RoleAccessPermissionValidator $roleAccessPermissionValidator,)
    {
    }

    public function execute(string $uuid, CreateAccessPermissionDTO $createAccessPermissionDTO): void
    {
        try {
            $role = $this->roleReaderRepository->getRoleByUUID($uuid);

            $accessPermissions = $this->parsePayload($createAccessPermissionDTO);
            $accessUuids = array_keys($accessPermissions);
            $permissionUuids = array_merge(...array_values($accessPermissions));

            $this->roleAccessPermissionValidator->isPermissionAlreadyAssignedToRoleAccess($role, $accessUuids, $permissionUuids);

            $this->commandBus->dispatch(new CreateRoleAccessPermissionCommand($role, $createAccessPermissionDTO->getAccesses()));
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious();
        }
    }

    private function parsePayload(CreateAccessPermissionDTO $createAccessPermissionDTO): array
    {
        $result = [];
        foreach ($createAccessPermissionDTO->getAccesses() ?? [] as $access) {
            $accessUuid = $access['uuid'] ?? null;
            $permissions = $access['permissions'] ?? [];

            if ($accessUuid && is_array($permissions)) {
                $result[$accessUuid] = $permissions;
            }
        }

        return $result;
    }

    private function getAccessesUUIDFromParsedPayload(array $data): array
    {
        return array_keys($data);
    }

    private function getPermissionsUUIDFromParsedPayload(array $data): array
    {
        $result = [];
        foreach ($data as $permissions) {
            foreach ($permissions as $permission) {
                if (in_array($permission, $result)) {
                    continue;
                }
                $result[] = $permission;
            }
        }

        return $result;
    }
}
