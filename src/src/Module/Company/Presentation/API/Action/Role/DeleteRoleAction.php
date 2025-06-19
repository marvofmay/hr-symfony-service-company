<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Role;

use App\Module\Company\Application\Command\Role\DeleteRoleCommand;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class DeleteRoleAction
{
    public function __construct(private MessageBusInterface $commandBus, private RoleReaderInterface $roleReaderRepository,)
    {}

    public function execute(string $uuid): void
    {
        try {
            $this->commandBus->dispatch(new DeleteRoleCommand($this->roleReaderRepository->getRoleByUUID($uuid)));
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious();
        }
    }
}
