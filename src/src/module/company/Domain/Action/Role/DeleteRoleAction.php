<?php

declare(strict_types = 1);

namespace App\module\company\Domain\Action\Role;

use App\module\company\Application\Command\Role\DeleteRoleCommand;
use App\module\company\Domain\Entity\Role;
use Symfony\Component\Messenger\MessageBusInterface;

class DeleteRoleAction
{
    public function __construct(private readonly MessageBusInterface $commandBus, private Role $role) {}

    public function setRoleToDelete(Role $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function execute(): void
    {
        $this->commandBus->dispatch(new DeleteRoleCommand($this->role));
    }
}