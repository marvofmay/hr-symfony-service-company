<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Role;

use App\Module\Company\Application\Command\Role\CreateRoleCommand;
use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Interface\Role\RoleCreatorInterface;
use App\Module\Company\Domain\Interface\Role\RoleWriterInterface;
use App\Module\System\Domain\Enum\CommandDataMapperKindEnum;
use App\Module\System\Domain\Factory\CommandDataMapperFactory;

final readonly class RoleCreator implements RoleCreatorInterface
{
    public function __construct(
        private RoleWriterInterface $roleWriterRepository,
        private CommandDataMapperFactory $commandDataMapperFactory,
    )
    {
    }

    public function create(CreateRoleCommand $command): void
    {
        $role = new Role();
        $mapper = $this->commandDataMapperFactory->getMapper(CommandDataMapperKindEnum::COMMAND_MAPPER_ROLE);
        $mapper->map($role, $command);

        $this->roleWriterRepository->saveRoleInDB($role);
    }
}
