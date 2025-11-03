<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Role;

use App\Common\Domain\Enum\DeleteTypeEnum;
use App\Module\Company\Application\Command\Role\AssignAccessesCommand;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use App\Module\System\Domain\Interface\RoleAccess\RoleAccessWriterInterface;

final readonly class RoleAccessDeleter
{
    public function __construct(
        private RoleReaderInterface $roleReaderRepository,
        private RoleAccessWriterInterface $roleAccessWriterRepository,
    )
    {
    }

    public function delete(AssignAccessesCommand $command): void
    {
        $role = $this->roleReaderRepository->getRoleByUUID($command->roleUUID);
        $this->roleAccessWriterRepository->deleteRoleAccessesByRoleInDB($role, DeleteTypeEnum::HARD_DELETE);
    }
}
