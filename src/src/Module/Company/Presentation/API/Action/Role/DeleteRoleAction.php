<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Role;

use App\Module\Company\Application\Command\Role\DeleteRoleCommand;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class DeleteRoleAction
{
    public function __construct(private readonly MessageBusInterface $commandBus, private readonly RoleReaderInterface $roleReaderRepository,)
    {}

    public function execute(string $uuid): void
    {
        $this->commandBus->dispatch(new DeleteRoleCommand($this->roleReaderRepository->getRoleByUUID($uuid)));
    }
}
