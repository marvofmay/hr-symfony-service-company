<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Role;

use App\Module\Company\Application\Command\Role\CreateRoleAccessCommand;
use App\Module\Company\Application\Validator\RoleAccess\RoleAccessValidator;
use App\Module\Company\Domain\DTO\Role\CreateAccessDTO;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use App\Module\System\Domain\Interface\Access\AccessReaderInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class CreateRoleAccessAction
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private RoleReaderInterface $roleReaderRepository,
        private AccessReaderInterface $accessReaderRepository,
        private RoleAccessValidator $roleAccessValidator,
    ) {
    }

    public function execute(string $uuid, CreateAccessDTO $createAccessDTO): void
    {
        try {
            $role = $this->roleReaderRepository->getRoleByUUID($uuid);
            $accesses = $this->accessReaderRepository->getAccessesByUUID($createAccessDTO->accessUUID);
            $this->roleAccessValidator->isAccessesAlreadyAssignedToRole($role, $accesses);

            $this->commandBus->dispatch(new CreateRoleAccessCommand($role, $accesses));
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious();
        }
    }
}
