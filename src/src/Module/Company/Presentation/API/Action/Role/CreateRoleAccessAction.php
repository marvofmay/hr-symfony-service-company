<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Role;

use App\Module\Company\Application\Command\Role\CreateRoleAccessCommand;
use App\Module\Company\Domain\DTO\Role\CreateAccessDTO;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class CreateRoleAccessAction
{
    public function __construct(private MessageBusInterface $commandBus,)
    {
    }

    public function execute(CreateAccessDTO $createAccessDTO): void
    {
        $this->commandBus->dispatch(
            new CreateRoleAccessCommand(
                $createAccessDTO->getRoleUUID(),
                $createAccessDTO->getAccessUUID()
            )
        );
    }
}
