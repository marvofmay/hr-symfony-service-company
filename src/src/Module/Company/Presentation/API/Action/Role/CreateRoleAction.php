<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Role;

use App\Module\Company\Application\Command\Role\CreateRoleCommand;
use App\Module\Company\Application\Validator\Role\RoleValidator;
use App\Module\Company\Domain\DTO\Role\CreateDTO;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class CreateRoleAction
{
    public function __construct(private MessageBusInterface $commandBus, private RoleValidator $roleValidator,)
    {
    }

    public function execute(CreateDTO $createDTO): void
    {
        try {
            $this->roleValidator->isRoleNameAlreadyExists($createDTO->getName());
            $this->commandBus->dispatch(
                new CreateRoleCommand(
                    $createDTO->getName(),
                    $createDTO->getDescription()
                )
            );
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious();
        }
    }
}
