<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Role;

use App\Common\Domain\Abstract\CommandHandlerAbstract;
use App\Module\Company\Application\Command\Role\AssignPermissionsCommand;
use App\Module\Company\Application\Event\Role\RoleAssignedPermissionsEvent;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use App\Module\Company\Domain\Service\Role\AssignPermissionsPayloadParser;
use App\Module\Company\Domain\Service\Role\AssignPermissionsReferenceLoader;
use App\Module\Company\Domain\Service\Role\RoleAccessPermissionAssigner;
use App\Module\Company\Domain\Service\Role\RoleAccessPermissionDeleter;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'command.bus')]
final class AssignPermissionsCommandHandler extends CommandHandlerAbstract
{
    public function __construct(
        private readonly RoleReaderInterface $roleReaderRepository,
        private readonly RoleAccessPermissionAssigner $roleAccessPermissionAssigner,
        private readonly RoleAccessPermissionDeleter $roleAccessPermissionDeleter,
        private readonly AssignPermissionsPayloadParser $assignPermissionsPayloadParser,
        private readonly AssignPermissionsReferenceLoader $assignPermissionsReferenceLoader,
        private readonly EventDispatcherInterface $eventDispatcher,
        #[AutowireIterator(tag: 'app.role.assignPermissions.validator')] protected iterable $validators,
    ) {
    }

    public function __invoke(AssignPermissionsCommand $command): void
    {
        $this->validate($command);

        $role = $this->roleReaderRepository->getRoleByUUID($command->{AssignPermissionsCommand::ROLE_UUID});

        $parsedPayload = $this->assignPermissionsPayloadParser->parse($command);

        $this->assignPermissionsReferenceLoader->preload($parsedPayload);
        $accesses = $this->assignPermissionsReferenceLoader->accesses;

        foreach ($command->{AssignPermissionsCommand::ACCESSES} as $item) {
            $accessUUID = $item[AssignPermissionsCommand::ACCESS_UUID];
            $permissionsUUIDs = $item[AssignPermissionsCommand::PERMISSIONS_UUIDS] ?? [];

            $access = $accesses[$accessUUID] ?? null;
            if (!$access) {
                continue;
            }

            empty($permissionsUUIDs)
                ? $this->roleAccessPermissionDeleter->delete($role, $access)
                : $this->roleAccessPermissionAssigner->assign($role, $access, $permissionsUUIDs);
        }

        $this->eventDispatcher->dispatch(new RoleAssignedPermissionsEvent([
            AssignPermissionsCommand::ROLE_UUID => $command->roleUUID,
            AssignPermissionsCommand::ACCESSES  => $command->accesses,
        ]));
    }
}
