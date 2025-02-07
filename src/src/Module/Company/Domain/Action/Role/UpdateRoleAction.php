<?php

declare(strict_types = 1);

namespace App\Module\Company\Domain\Action\Role;

use App\Module\Company\Application\Command\Role\UpdateRoleCommand;
use App\Module\Company\Domain\Entity\Role;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Module\Company\Domain\DTO\Role\UpdateDTO;

class UpdateRoleAction
{
    public function __construct(private readonly MessageBusInterface $commandBus, private Role $selectOption)
    {
    }

    public function setRoleToUpdate(Role $selectOption): self
    {
        $this->selectOption = $selectOption;

        return $this;
    }

    public function getRole(): Role
    {
        return $this->selectOption;
    }

    public function execute(UpdateDTO $updateDTO): void
    {
        $this->commandBus->dispatch(
            new UpdateRoleCommand(
                $updateDTO->getUUID(),
                $updateDTO->getName(),
                $updateDTO->getDescription(),
                $this->getRole()
            )
        );
    }
}