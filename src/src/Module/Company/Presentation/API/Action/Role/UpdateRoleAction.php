<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Role;

use App\Module\Company\Application\Command\Role\UpdateRoleCommand;
use App\Module\Company\Application\Validator\Role\RoleValidator;
use App\Module\Company\Domain\DTO\Role\UpdateDTO;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class UpdateRoleAction
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private RoleReaderInterface $roleReaderRepository,
        private RoleValidator $roleValidator,
    ) {
    }

    public function execute(string $uuid, UpdateDTO $updateDTO): void
    {
        try {
            $role = $this->roleReaderRepository->getRoleByUUID($uuid);
            $this->roleValidator->isRoleNameAlreadyExists($updateDTO->getName(), $uuid);

            $this->commandBus->dispatch(new UpdateRoleCommand($updateDTO->getName(), $updateDTO->getDescription(), $role));
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious();
        }
    }
}
