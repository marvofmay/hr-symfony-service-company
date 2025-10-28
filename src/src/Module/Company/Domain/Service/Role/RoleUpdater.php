<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Role;

use App\Module\Company\Application\Command\Role\UpdateRoleCommand;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use App\Module\Company\Domain\Interface\Role\RoleUpdaterInterface;
use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;
use App\Module\System\Domain\Enum\CommandDataMapperKindEnum;
use App\Module\System\Domain\Factory\CommandDataMapperFactory;

final readonly class RoleUpdater implements RoleUpdaterInterface
{
    public function __construct(
        private RoleReaderInterface $roleReaderRepository,
        private RoleWriterInterface $roleWriterRepository,
        private CommandDataMapperFactory $commandDataMapperFactory,
    )
    {
    }

    public function update(UpdateRoleCommand $command): void
    {
        $role = $this->roleReaderRepository->getRoleByUUID($command->roleUUID);
        $mapper = $this->commandDataMapperFactory->getMapper(CommandDataMapperKindEnum::COMMAND_MAPPER_ROLE);
        $mapper->map($role, $command);
        $this->roleWriterRepository->saveRoleInDB($role);
    }
}
